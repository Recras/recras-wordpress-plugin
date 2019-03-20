registerBlockType('recras/onlinebooking', {
    title: __('Online booking', TEXT_DOMAIN),
    icon: 'admin-site',
    category: 'recras',

    attributes: {
        autoresize: recrasHelper.typeBoolean(true),
        id: recrasHelper.typeString(),
        redirect: recrasHelper.typeString(),
        show_times: recrasHelper.typeBoolean(false),
        use_new_library: recrasHelper.typeBoolean(true),
        prefill_enabled: recrasHelper.typeBoolean(false),
        product_amounts: recrasHelper.typeString(), // stored as JSON string
    },

    edit: withSelect((select) => {
        return {
            packages: select('recras/store').fetchPackages(false),
            pagesPosts: select('recras/store').fetchPagesPosts(),
        }
    })(props => {
        const {
            id,
            use_new_library,
            redirect,
            show_times,
            autoresize,
        } = props.attributes;
        const {
            packages,
            pagesPosts,
        } = props;
        const packagesMapped = Object.values(packages).map(mapPackage);
        let product_amounts;
        try {
            product_amounts = JSON.parse(props.attributes.product_amounts);
        } catch (e) {
            product_amounts = {};
        }
        let prefill_enabled = props.attributes.prefill_enabled || Object.keys(product_amounts).length > 0;

        let retval = [];
        const optionsPackageControl = {
            value: id,
            onChange: function(newVal) {
                props.setAttributes({
                    id: newVal,
                });
            },
            options: packagesMapped,
            placeholder: __('Pre-filled package', TEXT_DOMAIN),
            label: __('Pre-filled package (optional)', TEXT_DOMAIN),
        };
        const optionsNewLibraryControl = {
            checked: use_new_library,
            onChange: function(newVal) {
                props.setAttributes({
                    use_new_library: recrasHelper.parseBoolean(newVal),
                });
            },
            label: __('Use new method?', TEXT_DOMAIN),
        };
        let optionsShowTimesControl;
        let optionsPreFillControl;
        let preFillControls = [];
        let optionsRedirectControl;
        let optionsAutoresizeControl;
        if (use_new_library) {
            optionsShowTimesControl = {
                checked: show_times,
                onChange: function(newVal) {
                    props.setAttributes({
                        show_times: recrasHelper.parseBoolean(newVal),
                    });
                },
                label: __('Preview times in programme', TEXT_DOMAIN),
            };
            optionsPreFillControl = {
                checked: prefill_enabled,
                onChange: function(newVal) {
                    props.setAttributes({
                        prefill_enabled: recrasHelper.parseBoolean(newVal),
                    });
                },
                label: __('Pre-fill amounts (requires pre-filled package)', TEXT_DOMAIN),
            };
            optionsRedirectControl = {
                value: redirect,
                onChange: function(newVal) {
                    props.setAttributes({
                        redirect: newVal
                    });
                },
                options: pagesPosts,
                placeholder: __('i.e. https://www.recras.com/thanks/', TEXT_DOMAIN),
                label: __('Redirect after submission (optional, leave empty to not redirect)', TEXT_DOMAIN),
            };

            if (prefill_enabled && id && packages[id]) {
                let linesNoBookingSize = packages[id].regels.filter(function(line) {
                    return line.onlineboeking_aantalbepalingsmethode !== 'boekingsgrootte';
                });
                let linesBookingSize = packages[id].regels.filter(function(line) {
                    return line.onlineboeking_aantalbepalingsmethode === 'boekingsgrootte';
                });
                if (linesBookingSize.length > 0) {
                    preFillControls.push({
                        value: product_amounts.bookingsize,
                        onChange: function(newVal) {
                            product_amounts.bookingsize = newVal;

                            props.setAttributes({
                                product_amounts: JSON.stringify(product_amounts)
                            });
                        },
                        label: packages[id].weergavenaam || packages[id].arrangement,
                        type: 'number',
                        min: 0,
                    });
                }
                linesNoBookingSize.forEach(line => {
                    let ctrl = {
                        value: product_amounts[line.id],
                        onChange: function(newVal) {
                            product_amounts[line.id] = newVal;

                            props.setAttributes({
                                product_amounts: JSON.stringify(product_amounts)
                            });
                        },
                        label: line.beschrijving_templated,
                        type: 'number',
                        min: 0,
                    };
                    preFillControls.push(ctrl);
                });
            }
        } else {
            optionsAutoresizeControl = {
                checked: autoresize,
                onChange: function(newVal) {
                    props.setAttributes({
                        autoresize: recrasHelper.parseBoolean(newVal),
                    });
                },
                label: __('Auto resize iframe', TEXT_DOMAIN),
            };
        }

        retval.push(recrasHelper.elementText('Recras - ' + __('Online booking', TEXT_DOMAIN)));
        retval.push(el(SelectControl, optionsPackageControl));
        retval.push(el(ToggleControl, optionsNewLibraryControl));
        if (use_new_library) {
            retval.push(el(ToggleControl, optionsShowTimesControl));
            retval.push(el(ToggleControl, optionsPreFillControl));
            if (preFillControls.length) {
                preFillControls.forEach(ctrl => {
                    retval.push(el(TextControl, ctrl));
                });
            }
            retval.push(el(SelectControl, optionsRedirectControl));
        } else {
            retval.push(el(ToggleControl, optionsAutoresizeControl));
        }
        return retval;
    }),

    save: function(props) {
    },
});