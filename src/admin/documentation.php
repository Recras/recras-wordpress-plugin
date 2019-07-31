<h1><?php _e('Documentation', \Recras\Plugin::TEXT_DOMAIN); ?></h1>


<h2><?php _e('Recras settings', \Recras\Plugin::TEXT_DOMAIN); ?></h2>
<dl>
    <dt><?= __('Recras name', \Recras\Plugin::TEXT_DOMAIN);?></dt>
    <dd>If you log in to Recras at <code>https://mysite.recras.nl/</code> then your Recras name is <code>mysite</code>.</dd>
    <dt><?= __('Currency symbol', \Recras\Plugin::TEXT_DOMAIN);?></dt>
    <dd>Used in prices such as € 100,00. Set to € (Euro) by default.</dd>
    <dt><?= __('Decimal separator', \Recras\Plugin::TEXT_DOMAIN);?></dt>
    <dd>Used in prices such as € 100,00. Set to , (comma) by default.</dd>
    <dt><?= __('Use calendar widget', \Recras\Plugin::TEXT_DOMAIN);?></dt>
    <dd>By default, date and time pickers in contact forms use whatever the browser has available. Currently (July 2019) Internet Explorer (all versions) and Safari (desktop) do not have a native date picker and only see a text field. We have included a modern looking date picker that you can use.<br>
        <strong>Note:</strong> this setting only applies to standalone contact forms, not to contact forms used during "new style" online booking.
    </dd>
    <dt><?= __('Theme for online booking', \Recras\Plugin::TEXT_DOMAIN);?></dt>
    <dd>Which theme the new online booking method will use.
        <ul>
            <li>"No theme" leaves it up to you to properly style it.
            <li>"Basic theme" sets some default styling to make it look a bit nicer. You can still override everything with your own CSS.
            <li>"Recras Blue" is a theme with blue accents
        </ul>
    </dd>
    <dt><?= __('Enable Google Analytics integration?', \Recras\Plugin::TEXT_DOMAIN);?></dt>
    <dd>You can enable Google Analytics integration. This will only work if there is a global <code>ga</code> JavaScript object. This should almost always be the case, but if you find out it doesn't work, please contact us!</dd>
</dl>


<hr>
<h2><?php _e('Package options', \Recras\Plugin::TEXT_DOMAIN); ?></h2>
The following options are available:
<ol class="recrasOptionsList">
    <li>Package - <strong>required</strong> what package to use
    <li>Property to show - <strong>required</strong> what property to show. This can be any of the following:<ol>
        <li>Description - the long description of this package
        <li>Duration - the duration of this package (i.e. time between start of first activity and end of last activity)
        <li>Image tag - the package image, if present.
        <li>Minimum number of persons - the minimum number of persons needed for this package
        <li>Price p.p. excl. VAT - the price per person, excluding VAT
        <li>Price p.p. incl. VAT - same as above, but including VAT
            <li>Programme - the programme as an HTML table. For styling purposes, the table has a <code>recras-programme</code> class. For multi-day programmes every <code>tr</code> starting on a new day has a <code>new-day</code> class
        <li>Starting location - the starting location name of this package
        <li>Title - the title (display name) of the package
        <li>Total price excl. VAT - shows the total price, excluding VAT
        <li>Total price incl. VAT - same as above, but including VAT
            <li>Relative image URL - gives the package image URL, if present. Any surrounding HTML/CSS, such as an <code>&lt;img&gt;</code> tag or <code>background-image</code> attribute will have to be written manually for maximum flexibility. If you just want to output the image, use "Image tag" instead. When using quotation marks, be sure to use different marks in the shortcode and the surrounding code, or the image will not show.
    </ol>
    <li>Start time - only visible when "Programme" is selected - determines the starting time of a package. If not set, it will default to 00:00
    <li>Show header? - only visible when "Programme" is selected - determines if the header should be shown. Enabled by default
</ol>


<hr>
<h2><?php _e('Contact form options', \Recras\Plugin::TEXT_DOMAIN); ?></h2>
The following options are available:
<ol class="recrasOptionsList">
	<li>Contact form - <strong>required</strong> what form to use
	<li>Show title? - show the title of the contact form or not. Enabled by default
	<li>Show labels? - show the label for each element. Enabled by default. <strong>Note:</strong> showing labels is highly recommended. It is good for accessibility, and when they are not used it can lead to confusing results with radio buttons.
	<li>Show placeholders? - show the placeholder for each element. Enabled by default
	<li>Package - for forms where the user can select a package, setting this parameter will select the package automatically and hide the field for the user.
	<li>HTML element - show the contact form as definition list (default), ordered list, or table (not recommended for accessibility reasons).
	<li>Element for single choices - show fields where a single choice is made (i.e. Customer type) as drop-down list (default) or radio buttons.
	<li>Submit button text - the text for the form submission button. Defaults to "Send"
	<li>Redirect after submission - a page/post that the user is redirected to, after submitting the form successfully.
</ol>

<hr>
<h2><?php _e('Online booking options', \Recras\Plugin::TEXT_DOMAIN); ?></h2>
The following options are available:
<ol class="recrasOptionsList">
	<li>Pre-filled package - entering a package here will skip the package selection step
	<li>Use new method? - whether or not you want to use the new online booking method.
	<li>Preview times in programme - whether or not you want to preview times in the programme. Note: this is only available for the new online booking method.
	<li>Pre-fill amounts - <strong>Note:</strong> this is only available for the new online booking method, and required a pre-filled package. When enabled, this gives you the ability to pre-fill the amounts form. This can be useful, for example, for packages where you always have a fixed amount.
	<li>Redirect after submission - a page/post that the user is redirected to, after booking successfully. Note: this is only available for the new online booking method.
	<li>Auto resize iframe - enabled by default. Disable this if you have more than one Recras iframe on your page. Note: this is only available for the old online booking method.
</ol>

<hr>
<h2><?php _e('Availability calendar options', \Recras\Plugin::TEXT_DOMAIN); ?></h2>
The following options are available:
<ol class="recrasOptionsList">
	<li>Package - what package to use for the availability calendar
	<li>Auto resize iframe - enabled by default. Disable this if you have more than one Recras iframe on your page
</ol>

<hr>
<h2><?php _e('Voucher sales options', \Recras\Plugin::TEXT_DOMAIN); ?></h2>
The following options are available:
<ol class="recrasOptionsList">
	<li>Voucher template - when selected, this will skip the template selection step
	<li>Redirect after submission - a page/post that the user is redirected to, after submitting the form successfully.
</ol>

<hr>
<h2><?php _e('Voucher info options', \Recras\Plugin::TEXT_DOMAIN); ?></h2>
This widget has one option:
<ol class="recrasOptionsList">
	<li>Property to show - <strong>required</strong> what property to show. This can be any of the following:<ol>
        <li>Name
        <li>Price
        <li>Number of days valid
    </ol>
</ol>
