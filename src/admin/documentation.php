<h1><?php _e('Documentation', \Recras\Plugin::TEXT_DOMAIN); ?></h1>


<h2><?php _e('Recras settings', \Recras\Plugin::TEXT_DOMAIN); ?></h2>
<dl>
    <dt><?= __('Recras name', \Recras\Plugin::TEXT_DOMAIN);?></dt>
    <dd>If you log in to Recras at <code>https://mysite.recras.nl/</code> then your Recras name is <code>mysite</code>.</dd>
    <dt><?= __('Currency symbol', \Recras\Plugin::TEXT_DOMAIN);?></dt>
    <dd>Used in prices such as € 100,00. Set to € (Euro) by default.</dd>
    <dt><?= __('Decimal separator', \Recras\Plugin::TEXT_DOMAIN);?></dt>
    <dd>Used in prices such as € 100,00. Set to , (comma) by default.</dd>
    <dt><?= __('Use calendar widget for contact forms', \Recras\Plugin::TEXT_DOMAIN);?></dt>
    <dd>By default, date pickers in contact forms use whatever the browser has available. If you want to be able to style the date picker, we recommend to enable the date picker we have included with the plugin.<br>
        <strong>Note:</strong> this setting only applies to standalone contact forms, not to contact forms used in book processes.
    </dd>
    <dt><?= __('Theme for Recras integrations', \Recras\Plugin::TEXT_DOMAIN);?></dt>
    <dd>Which theme is used for book processes.
        <ol class="recrasOptionsList">
            <li>No theme - leaves it up to you to properly style it.
            <li>Basic theme - sets some default styling to make it look a bit nicer. You can still override everything with your own CSS.
            <li>Recras Blue - is a theme with blue accents
            <li>BP Green - is a theme with green accents
            <li>Reasonably Red - is a theme with red accents
        </ol>
    </dd>
</dl>


<hr>
<h2><?php _e('Packages', \Recras\Plugin::TEXT_DOMAIN); ?></h2>
<p>Packages can be added using the Recras/Package block (Gutenberg) or using the <span class="rDocsIcon dashicons dashicons-clipboard"></span> icon in the Classic Editor.</p>
<p>The following options are available:</p>
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
<h2><?php _e('Book processes', \Recras\Plugin::TEXT_DOMAIN); ?></h2>
<p>Book processes can be added using the Recras/Book process block (Gutenberg) or using the <span class="rDocsIcon dashicons dashicons-editor-ul"></span> icon in the Classic Editor.</p>
<p>The following option is available:</p>
<ol class="recrasOptionsList">
    <li>Book process - <strong>required</strong> what process to use
</ol>


<hr>
<h2><?php _e('Contact forms', \Recras\Plugin::TEXT_DOMAIN); ?></h2>
<p>Contact forms can be added using the Recras/Contact form block (Gutenberg) or using the <span class="rDocsIcon dashicons dashicons-email"></span> icon in the Classic Editor.</p>
<p>The following options are available:</p>
<ol class="recrasOptionsList">
	<li>Contact form - <strong>required</strong> what form to use
	<li>Show title? - show the title of the contact form or not. Enabled by default
	<li>Show labels? - show the label for each element. Enabled by default. <strong>Note:</strong> showing labels is highly recommended. It is good for accessibility, and when they are not used it can lead to confusing results with radio buttons.
	<li>Show placeholders? - show the placeholder for each element. Enabled by default
	<li>Package - for forms where the user can select a package, setting this parameter will select the package automatically and hide the field for the user.
	<li>HTML element - show the contact form as definition list (default), ordered list, or table (not recommended for accessibility reasons).
	<li>Element for single choices - show fields where a single choice is made (i.e. Customer type) as drop-down list (default) or radio buttons.
	<li>Submit button text - the text for the form submission button. Defaults to "Send"
	<li>Thank-you page - a page/post that the user is redirected to, after submitting the form successfully.
</ol>

<hr>
<h2><?php _e('Products', \Recras\Plugin::TEXT_DOMAIN); ?></h2>
<p>Products can be added using the Recras/Product block (Gutenberg) or using the <span class="rDocsIcon dashicons dashicons-cart"></span> icon in the Classic Editor.</p>
<p>The following options are available:</p>
<ol class="recrasOptionsList">
    <li>Product - <strong>required</strong> what product to use
    <li>Property to show - <strong>required</strong> what property to show. This can be any of the following:<ol>
        <li>Description (long) - the long description of this product
        <li>Description (short) - the short description of this product
        <li>Duration - the duration of this product
        <li>Image tag - the product image, if present.
        <li>Image URL - gives the product image URL, if present. Any surrounding HTML/CSS, such as an <code>&lt;img&gt;</code> tag or <code>background-image</code> attribute will have to be written manually for maximum flexibility. If you just want to output the image, use "Image tag" instead. When using quotation marks, be sure to use different marks in the shortcode and the surrounding code, or the image will not show.
        <li>Minimum amount - the minimum amount needed for this product
        <li>Price (incl. VAT) - the product price, including VAT
        <li>Title - the title (display name) of the product
    </ol>
</ol>

<hr>
<h2><?php _e('Voucher sales', \Recras\Plugin::TEXT_DOMAIN); ?></h2>
<p>Voucher sales can be integrated using a book process.</p>

<hr>
<h2><?php _e('Voucher info', \Recras\Plugin::TEXT_DOMAIN); ?></h2>
<p>Voucher info can be integrated using the Recras/Voucher info block (Gutenberg) or using the <span class="rDocsIcon dashicons dashicons-money"></span> icon in the Classic Editor.</p>
<p>The following options are available:</p>
<ol class="recrasOptionsList">
    <li>Voucher template - what voucher template to show the info of
	<li>Property to show - <strong>required</strong> what property to show. This can be any of the following:<ol>
        <li>Name
        <li>Price
        <li>Number of days valid
    </ol>
</ol>
