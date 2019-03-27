const el = wp.element.createElement;
const { registerBlockType } = wp.blocks;
const {
    SelectControl,
    TextControl,
    ToggleControl,
} = wp.components;

const {
    registerStore,
    withSelect,
} = wp.data;

const TEXT_DOMAIN = 'recras-wp';
const { __ } = wp.i18n;

const recrasHelper = {
    serverSideRender: () => null,

    elementInfo: (text) => {
        return el(
            'p',
            {
                class: 'recrasInfoText',
            },
            text
        );
    },
    elementText: (text) => {
        return el(
            'div',
            null,
            text
        );
    },
    lockSave: (lockName, bool) => {
        if (bool) {
            wp.data.dispatch('core/editor').lockPostSaving(lockName);
        } else {
            wp.data.dispatch('core/editor').unlockPostSaving(lockName);
        }
    },

    typeBoolean: (defVal) => ({
        default: (defVal !== undefined) ? defVal : true,
        type: 'boolean',
    }),
    typeString: (defVal) => ({
        default: (defVal !== undefined) ? defVal : '',
        type: 'string',
    }),
};

const mapSelect = function(label, value) {
    return {
        label: label,
        value: value,
    };
};
const mapContactForm = function(idName) {
    return mapSelect(idName[1], idName[0]);
};
const mapPackage = function(pack) {
    return mapSelect(pack.arrangement, pack.id);
};
const mapPagesPosts = function(pagePost, prefix) {
    // SelectControl does not support optgroups :(
    // https://github.com/WordPress/gutenberg/issues/8426
    return mapSelect(prefix + pagePost.title.rendered, pagePost.link);
};
const mapProduct = function(product) {
    return mapSelect(product.naam, product.id);
};
const mapVoucherTemplate = function(template) {
    return mapSelect(template.name, template.id);
};

const recrasActions = {
    fetchAPI(path) {
        return {
            type: 'FETCH_API',
            path,
        }
    },

    setContactForms(contactForms) {
        return {
            type: 'SET_FORMS',
            contactForms,
        }
    },

    setPackages(packages) {
        return {
            type: 'SET_PACKAGES',
            packages,
        }
    },

    setPagesPosts(pagesPosts) {
        return {
            type: 'SET_PAGES_POSTS',
            pagesPosts,
        }
    },

    setProducts(products) {
        return {
            type: 'SET_PRODUCTS',
            products,
        }
    },

    setVoucherTemplates(voucherTemplates) {
        return {
            type: 'SET_VOUCHERS',
            voucherTemplates,
        }
    },
};
const recrasStore = registerStore('recras/store', {
    reducer(state = {
        contactForms: {},
        packages: {},
        pagesPosts: {},
        products: {},
        voucherTemplates: {},
    }, action) {
        switch (action.type) {
            case 'SET_FORMS':
                return {
                    ...state,
                    contactForms: action.contactForms,
                };
            case 'SET_PACKAGES':
                return {
                    ...state,
                    packages: action.packages,
                };
            case 'SET_PAGES_POSTS':
                return {
                    ...state,
                    pagesPosts: action.pagesPosts,
                };
            case 'SET_PRODUCTS':
                return {
                    ...state,
                    products: action.products,
                };
            case 'SET_VOUCHERS':
                return {
                    ...state,
                    voucherTemplates: action.voucherTemplates,
                };
        }

        return state;
    },
    recrasActions,
    selectors: {
        fetchContactForms(state) {
            const { contactForms } = state;
            return contactForms;
        },
        fetchPackages(state) {
            const { packages } = state;
            return packages;
        },
        fetchPagesPosts(state) {
            const { pagesPosts } = state;
            return pagesPosts;
        },
        fetchProducts(state) {
            const { products } = state;
            return products;
        },
        fetchVoucherTemplates(state) {
            const { voucherTemplates } = state;
            return voucherTemplates;
        },
    },
    controls: {
        FETCH_API(action) {
            return wp.apiFetch({
                path: action.path,
            });
        }
    },
    resolvers: {
        // * makes it a generator function
        * fetchContactForms(state) {
            let forms = yield recrasActions.fetchAPI('recras/contactforms');
            forms = Object.entries(forms);
            // Add empty value as first option, otherwise the first "real" value will be selected and Gutenberg will not store it
            forms.unshift([0, '']);
            forms = forms.map(mapContactForm);

            return recrasActions.setContactForms(forms);
        },
        * fetchPackages(mapSelect) {
            let packages = yield recrasActions.fetchAPI('recras/packages');
            if (mapSelect) {
                packages = Object.values(packages).map(mapPackage);
            }

            return recrasActions.setPackages(packages);
        },
        * fetchPagesPosts(state) {
            let pagesPosts = [{
                label: '',
                value: '',
            }];

            let pages = yield recrasActions.fetchAPI('wp/v2/pages');
            pages = pages.map(p => {
                return mapPagesPosts(p, __('Page: ', TEXT_DOMAIN));
            });
            pagesPosts = pagesPosts.concat(pages);

            let posts = yield recrasActions.fetchAPI('wp/v2/posts');
            posts = posts.map(p => {
                return mapPagesPosts(p, __('Post: ', TEXT_DOMAIN));
            });
            pagesPosts = pagesPosts.concat(posts);

            return recrasActions.setPagesPosts(pagesPosts);
        },
        * fetchProducts(state) {
            let products = yield recrasActions.fetchAPI('recras/products');
            products = Object.values(products).map(mapProduct);

            return recrasActions.setProducts(products);
        },
        * fetchVoucherTemplates(state) {
            let vouchers = yield recrasActions.fetchAPI('recras/vouchers');
            vouchers = Object.values(vouchers).map(mapVoucherTemplate);

            return recrasActions.setVoucherTemplates(vouchers);
        },
    }
});
