registerBlockType('recras/voucher-info', {
    title: __('Voucher info', TEXT_DOMAIN),
    icon: 'money',
    category: 'recras',

    attributes: {
        id: recrasHelper.typeString(),
        show: recrasHelper.typeString(name),
    },

    edit: withSelect((select) => {
        return {
            voucherTemplates: select('recras/store').fetchVoucherTemplates(),
        }
    })(props => {
        if (!recrasOptions.subdomain) {
            return recrasHelper.elementNoRecrasName();
        }

        const {
            id,
            show,
        } = props.attributes;
        const {
            voucherTemplates,
        } = props;

        let retval = [];
        const optionsIDControl = {
            value: id,
            onChange: function(newVal) {
                recrasHelper.lockSave('voucherID', !newVal);
                props.setAttributes({
                    id: newVal,
                });
            },
            options: voucherTemplates,
            label: __('Voucher template', TEXT_DOMAIN),
        };

        const optionsShowWhatControl = {
            value: show,
            onChange: function(newVal) {
                props.setAttributes({
                    show: newVal
                });
            },
            options: [
                {
                    value: 'name',
                    label: __('Name', TEXT_DOMAIN),
                },
                {
                    value: 'price',
                    label: __('Price', TEXT_DOMAIN),
                },
                {
                    value: 'validity',
                    label: __('Number of days valid', TEXT_DOMAIN),
                },
            ],
            label: __('Property to show', TEXT_DOMAIN),
        };

        retval.push(recrasHelper.elementText('Recras - ' + __('Voucher info', TEXT_DOMAIN)));
        retval.push(el(SelectControl, optionsIDControl));
        retval.push(el(SelectControl, optionsShowWhatControl));

        return retval;
    }),

    save: recrasHelper.serverSideRender,
});
