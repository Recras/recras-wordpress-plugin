window.fetchedPackageOptions = [];

registerGutenbergBlock('recras/bookprocess', {
    title: wp.i18n.__('Book process', TEXT_DOMAIN),
    icon: 'editor-ul',
    category: 'recras',
    example: {
        attributes: {
            id: null,
            initial_widget_value: null,
            hide_first_widget: false,
        },
    },

    attributes: {
        id: recrasHelper.typeString(),
        initial_widget_value: recrasHelper.typeString(),
        hide_first_widget: recrasHelper.typeBoolean(false),
    },

    edit: withSelect((select) => {
        return {
            bookprocesses: select('recras/store').fetchBookprocesses(),
        }
    })(props => {
        if (!recrasOptions.subdomain) {
            return recrasHelper.elementNoRecrasName();
        }

        let {
            id,
            initial_widget_value,
            hide_first_widget,
        } = props.attributes;
        const {
            bookprocesses,
        } = props;

        const mapBookprocess = function(idBookprocess) {
            return mapSelect(idBookprocess[1].name, idBookprocess[0]);
        };

        let retval = [];

        retval.push(
            recrasHelper.elementText(
                'Recras - ' + wp.i18n.__('Book process', TEXT_DOMAIN)
            )
        );

        const optionsIDControl = {
            value: id,
            onChange: function(newVal) {
                recrasHelper.lockSave('bookprocessID', !newVal);
                props.setAttributes({
                    id: newVal,
                    initial_widget_value: null,
                });
            },
            options: Object.entries(bookprocesses).map(mapBookprocess),
            label: wp.i18n.__('Book process', TEXT_DOMAIN),
        };
        if (Object.keys(bookprocesses).length > 0) {
            let bpArray = Object.entries(bookprocesses);
            if (Object.keys(bookprocesses).length === 1 || !id) {
                props.setAttributes({
                    id: bpArray[0][0],
                });
            }
        }
        retval.push(createEl(compSelectControl, optionsIDControl));

        let firstWidgetValueToggle = !!initial_widget_value; //TODO: initial_widget_value is empty when toggling, so this is always false
        //console.log('firstWidgetValueToggle is ', firstWidgetValueToggle, ', based on ', initial_widget_value); //DEBUG
        if (id) {
            const firstWidgetType = bookprocesses[id]?.firstWidget;
            const firstWidgetMayBePrefilled = ['booking.startdate', 'package'].includes(firstWidgetType);
            if (firstWidgetMayBePrefilled) {
                const optionsFirstWidgetValueToggle = {
                    checked: firstWidgetValueToggle,
                    onChange: function(newVal) {
                        console.log('Toggling firstWidgetValueToggle to', newVal); //DEBUG
                        firstWidgetValueToggle = newVal;
                        if (!newVal) {
                            props.setAttributes({
                                initial_widget_value: null,
                                hide_first_widget: false,
                            });
                            initial_widget_value = null;
                            hide_first_widget = false;
                        }
                    },
                    label: wp.i18n.__('Pre-fill first widget?', TEXT_DOMAIN),
                };
                retval.push(createEl(compToggleControl, optionsFirstWidgetValueToggle));

                if (firstWidgetValueToggle || true) { //TODO: DEBUG
                    if (firstWidgetType === 'booking.startdate') {
                        const optionsFirstWidgetDateControl = {
                            locale: dateSettings.l10n.locale,
                            value: initial_widget_value,
                            onChange: function(newVal) {
                                props.setAttributes({
                                    initial_widget_value: newVal
                                });
                            },
                            currentDate: initial_widget_value,
                        };
                        retval.push(recrasHelper.DatePickerControl(
                            wp.i18n.__('Pre-fill date?', TEXT_DOMAIN),
                            optionsFirstWidgetDateControl
                        ));
                    } else if (firstWidgetType === 'package') {
                        if (window.fetchedPackageOptions[id]) {
                            const optionsPackageIDControl = {
                                value: initial_widget_value,
                                onChange: function(newVal) {
                                    console.log('pck', newVal);
                                    props.setAttributes({
                                        initial_widget_value: newVal,
                                    });
                                },
                                options: window.fetchedPackageOptions[id],
                                label: wp.i18n.__('Package', TEXT_DOMAIN),
                            };
                            if (Object.keys(window.fetchedPackageOptions[id]).length === 1) {
                                props.setAttributes({
                                    initial_widget_value: window.fetchedPackageOptions[id].value,
                                });
                            }
                            retval.push(createEl(compSelectControl, optionsPackageIDControl));
                        } else {
                            //TODO: maybe do this in the backend so we can cache the results
                            fetch('https://' + recrasOptions.subdomain + '.recras.nl/api2/bookprocesses/book/' + id + '/1', {
                                method: 'POST',
                            }).then(res => res.json()).then(json => {
                                const firstWidget = json.form[0];
                                if (firstWidget.recrastype === 'package') {
                                    const options = firstWidget.inputs
                                        ? firstWidget.inputs.map(pi => pi.inputs[0])
                                        : firstWidget.options;
                                    window.fetchedPackageOptions[id] = options;
                                    props.setAttributes({
                                        initial_widget_value: options[0].value,
                                    });
                                }
                            });
                            retval.push(recrasHelper.elementInfo(wp.i18n.__('Fetching allowed packages...', TEXT_DOMAIN)));
                        }
                    } else {
                        retval.push(recrasHelper.elementInfo(wp.i18n.__('Pre-filling a value is unsupported for the first widget in this book process.', TEXT_DOMAIN)));
                    }

                    const optionsHideFirstWidgetControl = {
                        checked: hide_first_widget,
                        onChange: function(newVal) {
                            props.setAttributes({
                                hide_first_widget: newVal,
                            });
                        },
                        label: wp.i18n.__('Hide first widget?', TEXT_DOMAIN),
                    };
                    retval.push(createEl(compToggleControl, optionsHideFirstWidgetControl));
                }
            }
        }

        return retval;
    }),

    save: recrasHelper.serverSideRender,
});
