registerGutenbergBlock('recras/availability', {
    title: wp.i18n.__('Availability calendar', TEXT_DOMAIN),
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
            packages: select('recras/store').fetchPackagesForAvailability(),
        }
    })(props => {
        if (!recrasOptions.instance) {
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
        retval.push(recrasHelper.elementText('Recras - ' + wp.i18n.__('Availability calendar', TEXT_DOMAIN)));

        if (packages.length === 0) {
            retval.push(recrasHelper.elementText(wp.i18n.__('No suitable packages found', TEXT_DOMAIN)));
            return retval;
        }
        const optionsPackageControl = {
            value: id,
            onChange: function(newVal) {
                recrasHelper.lockSave('availabilityPackage', !newVal);
                props.setAttributes({
                    id: newVal,
                });
            },
            options: packages,
            label: wp.i18n.__('Package', TEXT_DOMAIN),
        };
        const optionsAutoresizeControl = {
            checked: autoresize,
            onChange: function(newVal) {
                props.setAttributes({
                    autoresize: newVal,
                });
            },
            label: wp.i18n.__('Auto resize iframe', TEXT_DOMAIN),
        };

        retval.push(createEl(compSelectControl, optionsPackageControl));
        retval.push(createEl(compToggleControl, optionsAutoresizeControl));
        return retval;
    }),

    save: recrasHelper.serverSideRender,
});
