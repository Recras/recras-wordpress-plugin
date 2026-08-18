registerGutenbergBlock('recras/bookprocess', {
    title: wp.i18n.__('Book process', TEXT_DOMAIN),
    apiVersion: 3,
    icon: 'editor-ul',
    category: 'recras',
    example: {
        attributes: {
            id: null,
            initial_widget_value: null,
            voucher_quantity: null,
            hide_first_widget: false,
        },
    },

    attributes: {
        id: recrasHelper.typeString(),
        initial_widget_value: recrasHelper.typeString(),
        voucher_quantity: recrasHelper.typeString(),
        hide_first_widget: recrasHelper.typeBoolean(false),
    },

    edit: withSelect((select) => {
        return {
            bookprocesses: select('recras/store').fetchBookprocesses(),
        }
    })(props => {
        if (!recrasOptions.instance) {
            return recrasHelper.elementNoRecrasName();
        }

        let {
            id,
            initial_widget_value,
            voucher_quantity,
            hide_first_widget,
        } = props.attributes;
        const {
            bookprocesses,
        } = props;

        const mapBookprocess = function(idBookprocess) {
            return mapSelect(
                idBookprocess[1].name + ' (ID: ' + idBookprocess[0] + ')',
                idBookprocess[0]
            );
        };

        let retval = [];

        retval.push(
            recrasHelper.elementText(
                'Recras - ' + wp.i18n.__('Book process', TEXT_DOMAIN)
            )
        );

        const bpSorted = Object.entries(bookprocesses).map(mapBookprocess).toSorted(selectSort);

        const optionsIDControl = {
            value: id,
            onChange: function(newVal) {
                recrasHelper.lockSave('bookprocessID', !newVal);
                props.setAttributes({
                    id: newVal,
                    initial_widget_value: null,
                    voucher_quantity: null,
                });
            },
            options: bpSorted,
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

        if (id) {
            const firstWidgetType = bookprocesses[id]?.firstWidget;
            const firstWidgetMayBePrefilled = ['package', 'voucher'].includes(firstWidgetType);
            if (firstWidgetMayBePrefilled) {
                let labelText;
                let placeHolderText;

                if (firstWidgetType === 'package') {
                    labelText = wp.i18n.__('Prefill package in first widget? (optional)', TEXT_DOMAIN)
                    placeHolderText = wp.i18n.__('Enter package ID. Leave empty to not prefill.', TEXT_DOMAIN);
                } else {
                    labelText = wp.i18n.__('Prefill voucher template in first widget? (optional)', TEXT_DOMAIN)
                    placeHolderText = wp.i18n.__('Enter voucher template ID. Leave empty to not prefill.', TEXT_DOMAIN);
                }

                const optionsFirstWidgetValueControl = {
                    locale: dateSettings.l10n.locale,
                    value: initial_widget_value,
                    onChange: function(newVal) {
                        props.setAttributes({
                            initial_widget_value: newVal
                        });
                    },
                    placeholder: placeHolderText,
                    label: labelText,
                };
                retval.push(createEl(compTextControl, optionsFirstWidgetValueControl));

                if (initial_widget_value && firstWidgetType === 'voucher') {
                    const optionsVoucherQuantityControl = {
                        locale: dateSettings.l10n.locale,
                        type: 'number',
                        min: 1,
                        value: voucher_quantity,
                        onChange: function(newVal) {
                            props.setAttributes({
                                voucher_quantity: newVal
                            });
                        },
                        placeholder: wp.i18n.__('Enter quantity', TEXT_DOMAIN),
                        label: wp.i18n.__('Enter quantity', TEXT_DOMAIN),
                    };
                    retval.push(createEl(compTextControl, optionsVoucherQuantityControl));
                }

                if (initial_widget_value) {
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
