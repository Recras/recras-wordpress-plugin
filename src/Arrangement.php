<?php
namespace Recras;

class Arrangement
{
    public const SHORTCODE = 'recras-package';
    private const SHOW_DEFAULT = 'title';

    /**
     * Add the [recras-package] shortcode
     *
     * @param array|string $attributes
     */
    public static function renderPackage($attributes): string
    {
        if (is_string($attributes)) {
            $attributes = [];
        }

        if (empty($attributes['id'])) {
            return __('Error: no ID set', 'recras');
        }
        if (!ctype_digit($attributes['id']) && !is_int($attributes['id'])) {
            return __('Error: ID is not a number', 'recras');
        }
        $showProperty = self::SHOW_DEFAULT;
        if (isset($attributes['show']) && in_array($attributes['show'], self::getValidOptions())) {
            $showProperty = $attributes['show'];
        }

        $instance = Settings::getInstance($attributes);
        if (!$instance) {
            return Plugin::noInstanceError();
        }

        $json = self::getPackage($instance, $attributes['id']);
        if (isset($json->error, $json->message)) {
            /* translators: Error message */
            return sprintf(__('Error: %s', 'recras'), $json->message);
        }
        if (is_null($json) || $json == new \stdClass()) { // comparing with === does not work
            return sprintf(
                /* translators: Package ID */
                __('Error: Package %d does not exist or may not be presented on a website', 'recras'),
                $attributes['id']
            );
        }

        switch ($showProperty) {
            case 'description':
                return $json->uitgebreide_omschrijving;
            case 'duration':
                return self::getDuration($json);
            case 'image_tag':
                if (!$json->image_filename) {
                    return '';
                }
                return '<img src="https://' . $instance . $json->image_filename . '" alt="' . htmlspecialchars(self::displayname($json)) . '">';
            case 'image_url':
                if (!$json->image_filename) {
                    return '';
                }
                return $json->image_filename;
            case 'location':
                return self::getLocation($json);
            case 'persons':
                return '<span class="recras-persons">' . $json->aantal_personen . '</span>';
            case 'price_pp_excl_vat':
                return Price::format($json->prijs_pp_exc);
            case 'price_pp_incl_vat':
                return Price::format($json->prijs_pp_inc);
            case 'price_total_excl_vat':
                return Price::format($json->prijs_totaal_exc);
            case 'price_total_incl_vat':
                return Price::format($json->prijs_totaal_inc);
            case 'program':
            case 'programme':
                $lines = self::getFilteredLines($json);

                if (count($lines) === 0) {
                    return __('Error: programme is empty', 'recras');
                }

                $startTime = (isset($attributes['starttime']) ? $attributes['starttime'] : '00:00');
                $showHeader = !isset($attributes['showheader']) || Settings::parseBoolean($attributes['showheader']);
                return self::generateProgramme($lines, $startTime, $showHeader);
            case 'title':
                return '<span class="recras-title">' . self::displayname($json) . '</span>';
            default:
                return __('Error: unknown option', 'recras');
        }
    }


    /**
     * Clear package cache (transients)
     */
    public static function clearCache(): int
    {
        $instance = Settings::getInstance();
        $errors = 0;

        $packages = array_keys(self::getPackages($instance));
        foreach ($packages as $id) {
            $name = $instance . '_arrangement_' . $id;
            if (Transient::get($name)) {
                $errors += Transient::delete($name);
            }
        }
        $errors += Transient::delete($instance . '_arrangements');

        return $errors;
    }


    private static function displayname(\stdClass $json): string
    {
        if ($json->weergavenaam) {
            return $json->weergavenaam;
        }
        return $json->arrangement;
    }


    private static function formatInterval(\DateInterval $interval): string
    {
        $isoString = 'P';

        if ($interval->days) {
            $isoString .= $interval->format('%aD');
        } elseif ($interval->y || $interval->m || $interval->d) {
            $isoString .= $interval->format('%yY%mM%dD');
        }

        $isoString .= $interval->format('T%hH%iM%sS');

        return $isoString;
    }


    private static function linesToProgramme(array $lines, \DateTime $pckBegin): array
    {
        return array_values(array_map(function (\stdClass $line) use ($pckBegin) {
            $begin = new \DateTime($line->begin);

            if ($line->eind) {
                $eind = new \DateTime($line->eind);
                $endFormatted = self::formatInterval($pckBegin->diff($eind));
                $duration = self::formatInterval($begin->diff($eind));
            } else {
                $endFormatted = null;
                $duration = null;
            }

            return (object) [
                'begin' => self::formatInterval($pckBegin->diff($begin)),
                'description' => $line->beschrijving_templated,
                'duration' => $duration,
                'end' => $endFormatted,
            ];
        }, $lines));
    }


    /**
     * Generate the programme for a package
     */
    public static function generateProgramme(array $lines, string $startTime = '00:00', bool $showHeader = true): string
    {
        $html = '<table class="recras-programme">';

        if ($showHeader) {
            $html .= '<thead>';
            $html .= '<tr><th>' . __('From', 'recras') . '<th>' . __('Until', 'recras') . '<th>' . __('Activity', 'recras');
            $html .= '</thead>';
        }

        // Lines is already sorted from the API, so the first element is guaranteed the first to start
        $first = reset($lines);

        // Convert lines to duration lines
        $programme = self::linesToProgramme($lines, new \DateTime($first->begin));

        $firstInProgramme = reset($programme);
        $startDatetime = new \DateTimeImmutable($startTime);
        $startDatetime = $startDatetime->add(new \DateInterval($firstInProgramme->duration));

        // Whether a package is multi-day can depend on the start time (i.e. a 4-hour package starting at 22:00)
        $progEnd = $firstInProgramme;
        $lastEnd = null;
        foreach ($programme as $line) {
            if ($line->end > $lastEnd) {
                $progEnd = $line;
            }
        }

        $endDatetime = new \DateTime($startTime);
        $endDatetime->add(new \DateInterval($progEnd->begin));
        $endDatetime->add(new \DateInterval($progEnd->duration));
        $isMultiDay = ($endDatetime->format('Ymd') > $startDatetime->format('Ymd'));

        $html .= '<tbody>';
        $lastDate = null;
        $day = 0;

        foreach ($programme as $activity) {
            $startDate = new \DateTime($startTime);
            $endDate = new \DateTime($startTime);
            $timeBegin = new \DateInterval($activity->begin);
            $lineDate = $startDate->add($timeBegin);
            $startFormatted = $lineDate->format('H:i');
            if ($isMultiDay && (is_null($lastDate) || $lineDate->format('Ymd') > $lastDate->format('Ymd'))) {
                ++$day;
                /* translators: Day number */
                $html .= '<tr class="recras-new-day"><th colspan="3">' . sprintf(__('Day %d', 'recras'), $day);
            }

            $html .= '<tr><td>' . $startFormatted;
            $html .= '<td>';
            if ($activity->end) {
                $timeEnd = new \DateInterval($activity->end);
                $html .= $endDate->add($timeEnd)->format('H:i');
            }
            $html .= '<td>' . $activity->description;
            $lastDate = $lineDate;
        }
        $html .= '</tbody>';
        $html .= '</table>';

        return $html;
    }


    /**
     * Get packages from the Recras API
     *
     * @return array|string
     */
    public static function getPackages(string $instance, bool $onlyOnline = false, bool $includeEmpty = true)
    {
        $json = Transient::get($instance . '_arrangements');
        if ($json === false) {
            try {
                $json = Http::get($instance, 'arrangementen');
            } catch (\Exception $e) {
                return $e->getMessage();
            }
            Transient::set($instance . '_arrangements', $json);
        }

        $packages = [];
        if ($includeEmpty) {
            $packages[0] = (object) [
                'arrangement' => '',
                'id' => null,
                'mag_online' => false,
            ];
        }
        foreach ($json as $pckg) {
            if (!$onlyOnline || $pckg->mag_online) {
                $packages[$pckg->id] = $pckg;
            }
        }
        return $packages;
    }


    /**
     * Get packages for a certain contact form from the Recras API
     *
     * @return array|string
     */
    public function getPackagesForContactForm(string $instance, int $contactformID, bool $includeEmpty)
    {
        $form = ContactForm::getForm($instance, $contactformID);
        if (is_string($form)) {
            // Not a form, but an error
            /* translators: Error message */
            return sprintf(__('Error: %s', 'recras'), $form);
        }

        $packages = [];
        if ($includeEmpty) {
            $packages[0] = '';
        }

        foreach ($form->Arrangementen as $pckg) {
            $packages[$pckg->id] = $pckg->arrangement;
        }
        natcasesort($packages);
        return $packages;
    }


    private static function getFilteredLines(\stdClass $json): array
    {
        if (!is_array($json->regels)) {
            $json->regels = (array) $json->regels;
        }

        return array_filter($json->regels, function ($line) {
            return $line->begin && $line->beschrijving_templated;
        });
    }


    /**
     * Get duration of a package
     */
    private static function getDuration(\stdClass $json): string
    {
        $lines = self::getFilteredLines($json);
        if (count($lines) === 0) {
            return __('No duration specified', 'recras');
        }

        $firstLine = min(array_map(function ($line) {
            return $line->begin;
        }, $lines));
        $lastLine = max(array_map(function ($line) {
            return $line->eind;
        }, $lines));

        $duration = (new \DateTime($firstLine))->diff(new \DateTime($lastLine));

        $durations = [];
        if ($duration->d) {
            $durations[] = $duration->d;
        }
        if ($duration->h) {
            $durations[] = $duration->h;
        }
        if ($duration->i) {
            $durations[] = str_pad($duration->i, 2, '0', STR_PAD_LEFT);
        } else {
            $durations[] = '00';
        }

        $html = '<span class="recras-duration">';
        $html .= implode(':', $durations);
        $html .= '</span>';

        return $html;
    }


    /**
     * Get the starting location of a package
     */
    private static function getLocation(\stdClass $json): string
    {
        if (isset($json->ontvangstlocatie)) {
            $location = $json->ontvangstlocatie;
        } else {
            $location = __('No location specified', 'recras');
        }
        return '<span class="recras-location">' . $location . '</span>';
    }


    /**
     * @return object|string
     */
    public static function getPackage(string $instance, int $id)
    {
        $json = Transient::get($instance . '_arrangement_' . $id);
        if ($json === false) {
            try {
                $json = Http::get($instance, 'arrangementen/' . $id);
            } catch (\Exception $e) {
                return $e->getMessage();
            }
            Transient::set($instance . '_arrangement_' . $id, $json);
        }
        return $json;
    }


    /**
     * Get all valid options for the "show" argument
     */
    public static function getValidOptions(): array
    {
        return ['description', 'duration', 'image_tag', 'image_url', 'location', 'persons', 'price_pp_excl_vat', 'price_pp_incl_vat', 'price_total_excl_vat', 'price_total_incl_vat', 'program', 'programme', 'title'];
    }


    /**
     * Show the TinyMCE shortcode generator package form
     */
    public static function showForm(): void
    {
        require_once(__DIR__ . '/../editor/form-arrangement.php');
    }
}
