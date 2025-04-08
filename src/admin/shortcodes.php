<h1><?php _e('Shortcodes', \Recras\Plugin::TEXT_DOMAIN); ?></h1>


<h2><?php _e('Packages', \Recras\Plugin::TEXT_DOMAIN); ?></h2>
<p>Packages can be added using the <kbd>[recras-package]</kbd> shortcode.</p>
<p>The following options are available:</p>
<ol class="recrasOptionsList">
    <li>Package - attribute <kbd>id</kbd>
    <li>Property to show - <kbd>show</kbd>. This can be any of the following:<ol>
        <li>Description - <kbd>description</kbd>
        <li>Duration - <kbd>duration</kbd>
        <li>Image tag - <kbd>image_tag</kbd>
        <li>Minimum number of persons - <kbd>persons</kbd>
        <li>Price p.p. excl. VAT - <kbd>price_pp_excl_vat</kbd>
        <li>Price p.p. incl. VAT - <kbd>price_pp_incl_vat</kbd>
        <li>Programme - <kbd>programme</kbd>
        <li>Starting location - <kbd>location</kbd>
        <li>Title - <kbd>title</kbd>
        <li>Total price excl. VAT - <kbd>price_total_excl_vat</kbd>
        <li>Total price incl. VAT - <kbd>price_total_incl_vat</kbd>
        <li>Relative image URL - <kbd>image_url</kbd>. When using quotation marks, be sure to use different marks in the shortcode and the surrounding code, or the image will not show. E.g. <kbd>&lt;img src="[recras-package id=42 show='image_url']"&gt;</kbd>
    </ol>
    <li>Start time - <kbd>starttime</kbd>, value is a 24-hour time string
    <li>Show header? - <kbd>showheader</kbd>, value is either <kbd>1</kbd> (yes) or <kbd>0</kbd> (no)
</ol>
<p>Example: <kbd>[recras-package id="42" show="programme" starttime="12:00" showheader="1"]</kbd></p>


<hr>
<h2><?php _e('Book processes', \Recras\Plugin::TEXT_DOMAIN); ?></h2>
<p>Book processes can be added using the <kbd>[recras-bookprocess]</kbd> shortcode.</p>
<p>The following options are available:</p>
<ol class="recrasOptionsList">
    <li>Book process - <kbd>id</kbd>
    <li>Initial widget value - <kbd>initial_widget_value</kbd>. If the first widget if "Package selection", the value is the ID of a package. If the first widget is any other type, the value is ignored.
    <li>Hide first widget? - <kbd>hide_first_widget</kbd>. Value is either <kbd>1</kbd> (yes) or <kbd>0</kbd> (no, default). This is only used when an initial widget value is present. Be careful, because if this is used when the value is invalid the book process will be unusable!
</ol>
<p>Example: <kbd>[recras-bookprocess id="9" initial_widget_value="42"]</kbd></p>


<hr>
<h2><?php _e('Contact forms', \Recras\Plugin::TEXT_DOMAIN); ?></h2>
<p>Contact forms can be added using the <kbd>[recras-contact]</kbd> shortcode.</p>
<p>The following options are available:</p>
<ol class="recrasOptionsList">
	<li>Contact form - <kbd>id</kbd>
	<li>Show title? - <kbd>showtitle</kbd>
	<li>Show labels? - <kbd>showlabels</kbd>
	<li>Show placeholders? - <kbd>showplaceholders</kbd>
	<li>Package - <kbd>arrangement</kbd>
	<li>HTML element - <kbd>element</kbd>, value is one of <kbd>dl</kbd> (recommended), <kbd>ol</kbd>, <kbd>table</kbd> (discouraged)
	<li>Element for single choices - <kbd>single_choice_element</kbd>, value is one of <kbd>select</kbd>, <kbd>radio</kbd>
    <li>Submit button text - <kbd>submitText</kbd>
    <li>Thank-you page - <kbd>redirect</kbd>
</ol>
<p>Example: <kbd>[recras-contact id="17" showtitle="0" showlabels="1" showplaceholders="1" submitText="Go!"]</kbd></p>


<hr>
<h2><?php _e('Products', \Recras\Plugin::TEXT_DOMAIN); ?></h2>
<p>Products can be added using the <kbd>recras-product</kbd> shortcode.</p>
<p>The following options are available:</p>
<ol class="recrasOptionsList">
    <li>Product - attribute <kbd>id</kbd>
    <li>Property to show - <kbd>show</kbd>. This can be any of the following:<ol>
        <li>Description (long) - <kbd>description_long</kbd>
        <li>Description (short) - <kbd>description</kbd>
        <li>Duration - <kbd>duration</kbd>
        <li>Image tag - <kbd>image_tag</kbd>
        <li>Image URL - <kbd>image_url</kbd>. When using quotation marks, be sure to use different marks in the shortcode and the surrounding code, or the image will not show. E.g. <kbd>&lt;img src="[recras-product id=42 show='image_url']"&gt;</kbd>
        <li>Minimum amount - <kbd>minimum_amount</kbd>
        <li>Price (incl. VAT) - <kbd>price_incl_vat</kbd>
        <li>Title - <kbd>title</kbd>
    </ol>
</ol>


<hr>
<h2><?php _e('Voucher info', \Recras\Plugin::TEXT_DOMAIN); ?></h2>
<p>Voucher info can be added using the <kbd>recras-voucher-info</kbd> shortcode.</p>
<p>The following options are available:</p>
<ol class="recrasOptionsList">
    <li>Voucher template - <kbd>id</kbd>
	<li>Property to show - <kbd>show</kbd>. Value is any of the following:<ol>
        <li>Name - <kbd>name</kbd>
        <li>Price - <kbd>price</kbd>
        <li>Validity (number of days, or date) - <kbd>validity</kbd>
    </ol>
</ol>
<p>Example: <kbd>[recras-voucher-info id="81" show="price"]</kbd></p>
