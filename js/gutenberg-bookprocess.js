registerGutenbergBlock('recras/bookprocess', {
    title: wp.i18n.__('Book process', TEXT_DOMAIN),
    icon: 'editor-ul',
    category: 'recras',
    example: {
        attributes: {
            id: null,
        },
    },

    attributes: {
        id: recrasHelper.typeString(),
    },

    edit: withSelect((select) => {
        return {
            bookprocesses: select('recras/store').fetchBookprocesses(),
        }
    })(props => {
        if (!recrasOptions.subdomain) {
            return recrasHelper.elementNoRecrasName();
        }

        const {
            id,
        } = props.attributes;
        const {
            bookprocesses,
        } = props;

        const mapBookprocess = function(idBookprocess) {
            return mapSelect(idBookprocess[1].name, idBookprocess[0]);
        };

        let retval = [];
        const optionsIDControl = {
            value: id,
            onChange: function(newVal) {
                recrasHelper.lockSave('bookprocessID', !newVal);
                props.setAttributes({
                    id: newVal,
                });
            },
            options: Object.entries(bookprocesses).map(mapBookprocess),
            label: wp.i18n.__('Book process', TEXT_DOMAIN),
        };
        if (Object.keys(bookprocesses).length === 1) {
            let bpArray = Object.entries(bookprocesses);
            props.setAttributes({
                id: bpArray[0][0],
            });
        }

        retval.push(
            recrasHelper.elementText(
                'Recras - ' + wp.i18n.__('Book process', TEXT_DOMAIN)
            )
        );

        retval.push(createEl(compSelectControl, optionsIDControl));

        return retval;
    }),

    save: recrasHelper.serverSideRender,
});
