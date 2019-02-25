registerBlockType('recras/availability', {
    title: __('Availability calendar'),
    icon: 'calendar-alt',
    category: 'recras',

    attributes: {
        id: recrasHelper.typeString(),
        autoresize: recrasHelper.typeBoolean(true),
    },

    edit: withSelect((select) => {
        return {
            packages: select('recras/store').fetchPackages(),
            pagesPosts: select('recras/store').fetchPagesPosts(),
        }
    })(props => {
        const {
            id,
            autoresize,
        } = props.attributes;
        const {
            packages,
        } = props;

        let retval = [];
        const optionsPackageControl = {
            selected: id,
            onChange: function(newVal) {
                props.setAttributes({
                    id: newVal,
                });
            },
            options: packages,
            label: __('Package'),
        };
        const optionsAutoresizeControl = {
            checked: autoresize,
            onChange: function(newVal) {
                props.setAttributes({
                    autoresize: recrasHelper.parseBoolean(newVal),
                });
            },
            label: __('Auto resize iframe'),
        };

        retval.push(recrasHelper.elementText('Recras - ' + __('Availability calendar')));
        retval.push(el(SelectControl, optionsPackageControl));
        retval.push(el(ToggleControl, optionsAutoresizeControl));
        return retval;
    }),

    save: recrasHelper.serverSideRender,
});
