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

        if (id) {
            const firstWidgetType = bookprocesses[id]?.firstWidget;
            const firstWidgetMayBePrefilled = ['package'].includes(firstWidgetType);
            if (firstWidgetMayBePrefilled) {
                const placeHolderText = (firstWidgetType === 'package')
                    ? wp.i18n.__('Enter package ID. Leave empty to not prefill.', TEXT_DOMAIN)
                    : wp.i18n.__('Enter date in YYYY-MM-DD format. Leave empty to not prefill.', TEXT_DOMAIN);
                const optionsFirstWidgetValueControl = {
                    locale: dateSettings.l10n.locale,
                    value: initial_widget_value,
                    onChange: function(newVal) {
                        props.setAttributes({
                            initial_widget_value: newVal
                        });
                    },
                    placeholder: placeHolderText,
                    label: wp.i18n.__('Prefill value for first widget? (optional)', TEXT_DOMAIN),
                };
                retval.push(createEl(compTextControl, optionsFirstWidgetValueControl));

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
