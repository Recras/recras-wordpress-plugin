registerBlockType('recras/availability', {
    title: __('Availability calendar', TEXT_DOMAIN),
    icon: 'calendar-alt',
    category: 'recras',
    example: {
        attributes: {
            id: null,
            autoresize: true,
        },
    },

    attributes: {
        id: recrasHelper.typeString(),
        autoresize: recrasHelper.typeBoolean(true),
    },

    edit: withSelect((select) => {
        return {
            packages: select('recras/store').fetchPackages(true, false),
            pagesPosts: select('recras/store').fetchPagesPosts(),
        }
    })(props => {
        if (!recrasOptions.subdomain) {
            return recrasHelper.elementNoRecrasName();
        }

        const {
            id,
            autoresize,
        } = props.attributes;
        const {
            packages,
        } = props;

        let retval = [];
        const optionsPackageControl = {
            value: id,
            onChange: function(newVal) {
                recrasHelper.lockSave('availabilityPackage', !newVal);
                props.setAttributes({
                    id: newVal,
                });
            },
            options: packages,
            label: __('Package', TEXT_DOMAIN),
        };
        const optionsAutoresizeControl = {
            checked: autoresize,
            onChange: function(newVal) {
                props.setAttributes({
                    autoresize: newVal,
                });
            },
            label: __('Auto resize iframe', TEXT_DOMAIN),
        };

        retval.push(recrasHelper.elementText('Recras - ' + __('Availability calendar', TEXT_DOMAIN)));
        retval.push(el(SelectControl, optionsPackageControl));
        retval.push(el(ToggleControl, optionsAutoresizeControl));
        return retval;
    }),

    save: recrasHelper.serverSideRender,
});
