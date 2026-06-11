<?php
namespace Recras;

/**
 * Regression tests for an unreachable Recras API.
 *
 * When the website's server cannot reach the Recras API (DNS failure, firewall,
 * timeout), widgets used to crash the whole page with a fatal TypeError instead
 * of showing an error message.
 */
class ApiUnreachableTest extends WordPressUnitTestCase
{
    private const CONNECTION_ERROR = 'cURL error 28: Connection timed out';
    private const FETCH_ERROR = 'Error: could not retrieve data from Recras. The error message received was: ' . self::CONNECTION_ERROR;

    private function simulateUnreachableApi(): callable
    {
        $filter = function () {
            return new \WP_Error('http_request_failed', self::CONNECTION_ERROR);
        };
        add_filter('pre_http_request', $filter);
        return $filter;
    }

    function testPackageShortcodeShowsErrorWhenApiIsUnreachable(): void
    {
        $filter = $this->simulateUnreachableApi();
        $content = $this->createPostAndGetContent('[recras-package id=7 recrasname=unreachable1.recras.nl]');
        remove_filter('pre_http_request', $filter);
        $this->assertEquals(self::FETCH_ERROR . "\n", $content, 'An unreachable Recras should show an error, not crash the page');
    }

    function testProductShortcodeShowsErrorWhenApiIsUnreachable(): void
    {
        $filter = $this->simulateUnreachableApi();
        $content = $this->createPostAndGetContent('[recras-product id=42 recrasname=unreachable2.recras.nl]');
        remove_filter('pre_http_request', $filter);
        $this->assertEquals(self::FETCH_ERROR . "\n", $content, 'An unreachable Recras should show an error, not crash the page');
    }

    function testVoucherInfoShortcodeShowsErrorWhenApiIsUnreachable(): void
    {
        $filter = $this->simulateUnreachableApi();
        $content = $this->createPostAndGetContent('[recras-voucher-info id=1 recrasname=unreachable3.recras.nl]');
        remove_filter('pre_http_request', $filter);
        $this->assertEquals(self::FETCH_ERROR . "\n", $content, 'An unreachable Recras should show an error, not crash the page');
    }

    function testBookprocessShortcodeShowsErrorWhenApiIsUnreachable(): void
    {
        $filter = $this->simulateUnreachableApi();
        $content = $this->createPostAndGetContent('[recras-bookprocess id=1 recrasname=unreachable4.recras.nl]');
        remove_filter('pre_http_request', $filter);
        $this->assertEquals(sprintf('Error: %s', self::FETCH_ERROR) . "\n", $content, 'An unreachable Recras should show an error, not crash the page');
    }

    function testContactFormShortcodeShowsErrorWhenApiIsUnreachable(): void
    {
        $filter = $this->simulateUnreachableApi();
        $content = $this->createPostAndGetContent('[recras-contact id=17 recrasname=unreachable5.recras.nl]');
        remove_filter('pre_http_request', $filter);
        $this->assertEquals(sprintf('Error: %s', self::FETCH_ERROR) . "\n", $content, 'An unreachable Recras should show an error, not crash the page');
    }

    function testClearPackageCacheReportsErrorWhenApiIsUnreachable(): void
    {
        delete_transient('recras_demo.recras.nl_arrangements');
        $filter = $this->simulateUnreachableApi();
        $errors = Arrangement::clearCache();
        remove_filter('pre_http_request', $filter);
        $this->assertEquals(1, $errors, 'Clearing the package cache when Recras is unreachable should report an error, not crash');
    }

    function testClearContactFormCacheReportsErrorWhenApiIsUnreachable(): void
    {
        delete_transient('recras_demo.recras.nl_contactforms');
        $filter = $this->simulateUnreachableApi();
        $errors = ContactForm::clearCache();
        remove_filter('pre_http_request', $filter);
        $this->assertEquals(1, $errors, 'Clearing the contact form cache when Recras is unreachable should report an error, not crash');
    }

    function testCachedErrorStringIsTreatedAsCacheMiss(): void
    {
        // Older plugin versions could cache an error message string instead of data
        set_transient('recras_unreachable6.recras.nl_arrangement_7', 'Error: could not retrieve data from Recras.');
        $package = (object) [
            'id' => 7,
            'weergavenaam' => 'Healed',
            'arrangement' => 'Healed',
        ];
        $filter = function () use ($package) {
            return [
                'headers' => [],
                'body' => json_encode($package),
                'response' => ['code' => 200, 'message' => 'OK'],
                'cookies' => [],
                'filename' => null,
            ];
        };
        add_filter('pre_http_request', $filter);
        $content = $this->createPostAndGetContent('[recras-package id=7 recrasname=unreachable6.recras.nl]');
        remove_filter('pre_http_request', $filter);
        $this->assertEquals('<span class="recras-title">Healed</span>' . "\n", $content, 'An error string cached by an older plugin version should be treated as a cache miss');
    }

    function testSettingsPageShowsConnectionError(): void
    {
        $filter = $this->simulateUnreachableApi();
        ob_start();
        Settings::addInputDomain(['field' => 'recras_domain']);
        $output = ob_get_clean();
        remove_filter('pre_http_request', $filter);
        $this->assertStringContainsString('Could not connect to your Recras: ' . self::CONNECTION_ERROR, $output, 'A connection error should be shown as such');
        $this->assertStringNotContainsString('Instance not found', $output, 'A connection error should not be presented as a missing instance');
    }

    function testSettingsPageShowsInstanceNotFoundOn404(): void
    {
        $requestsResponse = new \WpOrg\Requests\Response();
        $requestsResponse->status_code = 404;
        $filter = function () use ($requestsResponse) {
            return [
                'headers' => [],
                'body' => 'Instance not found',
                'response' => ['code' => 404, 'message' => 'Not Found'],
                'cookies' => [],
                'filename' => null,
                'http_response' => new \WP_HTTP_Requests_Response($requestsResponse),
            ];
        };
        add_filter('pre_http_request', $filter);
        ob_start();
        Settings::addInputDomain(['field' => 'recras_domain']);
        $output = ob_get_clean();
        remove_filter('pre_http_request', $filter);
        $this->assertStringContainsString('Instance not found!', $output, 'A 404 means the instance does not exist');
    }
}
