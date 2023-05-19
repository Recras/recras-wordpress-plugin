const createEl = wp.element.createElement;
const registerGutenbergBlock = wp.blocks.registerBlockType;
const compDatePicker = wp.components.DatePicker;
const compRadioControl = wp.components.RadioControl;
const compSelectControl = wp.components.SelectControl;
const compTextControl = wp.components.TextControl;
const compToggleControl = wp.components.ToggleControl;

const {
    registerStore,
    withSelect,
} = wp.data;

const dateSettings = wp.date.getSettings();
const TEXT_DOMAIN = 'recras';

const recrasHelper = {
    serverSideRender: () => null,

    DatePickerControl: (label, options) => {
        return createEl(
            'div',
            null,
            recrasHelper.elementLabel(label),
            createEl(compDatePicker, options)
        );
    },
    elementInfo: (text) => {
        // text may contain HTML (line breaks), so createInterpolateElement doesn't work for us
        return createEl(
            wp.element.RawHTML,
            null,
            '<p class="recrasInfoText">' + text + '</p>'
        );
    },
    elementLabel: (text) => {
        return createEl(
            'label',
            {
                class: 'components-base-control',
            },
            text
        );
    },
    elementNoRecrasName: () => {
        const settingsLink = `<a href="${ recrasOptions.settingsPage }" target="_blank">${ wp.i18n.__('Recras → Settings menu', TEXT_DOMAIN) }</a>`;
        return [
            recrasHelper.elementInfo(wp.i18n.sprintf(wp.i18n.__('Please enter your Recras name in the %s before adding widgets.', TEXT_DOMAIN), settingsLink)),
        ];
    },
    elementOption: (value, label) => {
        return createEl('option', { value: value }, label)
    },
    elementText: (text) => {
        return createEl(
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

const paramsWithPage = function(page) {
    const params = new URLSearchParams({
        page,
        per_page: 100, // WP has a hard limit of 100 posts per page
        orderby: 'title',
        order: 'asc',
        _fields: 'id,title,link', // We're only interested in these fields
    });
    return params.toString();
};
const mapSelect = function(label, value) {
    return {
        label: label,
        value: value,
    };
};
const mapContactForm = function(idName) {
    return mapSelect(idName[1].naam, idName[0]);
};
const mapPackage = function(pack) {
    return mapSelect(pack.arrangement, pack.id);
};
const mapCFPackages = function(cfPck) {
    let packages = Object.values(cfPck).map(mapPackage);
    if (packages.length) {
        packages.unshift({ label: '', value: 0, });
    }
    return packages;
};
const mapPagesPosts = function(pagePost) {
    return recrasHelper.elementOption(pagePost.link, pagePost.title.rendered);
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

    setBookprocesses(bookprocesses) {
        return {
            type: 'SET_BOOKPROCESSES',
            bookprocesses,
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
        bookprocesses: {},
        contactForms: {},
        packages: {},
        pagesPosts: {},
        products: {},
        voucherTemplates: {},
    }, action) {
        switch (action.type) {
            case 'SET_BOOKPROCESSES':
                return {
                    ...state,
                    bookprocesses: action.bookprocesses,
                };
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
        fetchBookprocesses(state) {
            const { bookprocesses } = state;
            return bookprocesses;
        },
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
        * fetchBookprocesses(state) {
            let bookprocesses = yield recrasActions.fetchAPI('recras/bookprocesses');

            return recrasActions.setBookprocesses(bookprocesses);
        },
        * fetchContactForms(state) {
            let forms = yield recrasActions.fetchAPI('recras/contactforms');

            return recrasActions.setContactForms(forms);
        },
        * fetchPackages(mapSelect, includeEmpty) {
            let packages = yield recrasActions.fetchAPI('recras/packages');
            if (includeEmpty) {
                packages[0] = {
                    arrangement: '',
                    id: 0,
                };
            }
            if (mapSelect) {
                packages = Object.values(packages).map(mapPackage);
            }

            return recrasActions.setPackages(packages);
        },
        * fetchPagesPosts(state) {
            let pagesPosts = [
                recrasHelper.elementOption('', ''),
            ];

            let page = 1;
            let pages = [];
            let isDone = false;
            while (!isDone) {
                const params = paramsWithPage(page);
                try {
                    let pagesNew = yield recrasActions.fetchAPI('wp/v2/pages?' + params);
                    pages.push(...pagesNew);
                    if (pagesNew.length === 0) {
                        isDone = true;
                    }
                    ++page;
                } catch (e) {
                    if (e.code === 'rest_post_invalid_page_number') {
                        isDone = true;
                    } else {
                        console.warn(e.code);
                    }
                }
            }

            pages = pages.map(p => mapPagesPosts(p));
            pagesPosts.push(createEl('optgroup', { label: wp.i18n.__('Pages', TEXT_DOMAIN) }, pages));

            page = 1;
            let posts = [];
            isDone = false;
            while (!isDone) {
                const params = paramsWithPage(page);
                try {
                    let postsNew = yield recrasActions.fetchAPI('wp/v2/posts?' + params);
                    posts.push(...postsNew);
                    if (postsNew.length === 0) {
                        isDone = true;
                    }
                    ++page;
                } catch (e) {
                    if (e.code === 'rest_post_invalid_page_number') {
                        isDone = true;
                    } else {
                        console.warn(e.code);
                    }
                }
            }

            posts = posts.map(p => mapPagesPosts(p));
            pagesPosts.push(createEl('optgroup', { label: wp.i18n.__('Posts', TEXT_DOMAIN) }, posts));

            return recrasActions.setPagesPosts(pagesPosts);
        },
        * fetchProducts(state) {
            let products = yield recrasActions.fetchAPI('recras/products');
            products = Object.values(products).map(mapProduct);

            return recrasActions.setProducts(products);
        },
        * fetchVoucherTemplates(includeEmpty) {
            let vouchers = yield recrasActions.fetchAPI('recras/vouchers');
            vouchers = Object.values(vouchers).map(mapVoucherTemplate);

            if (includeEmpty) {
                vouchers.unshift({
                    name: '',
                    id: 0,
                });
            }

            return recrasActions.setVoucherTemplates(vouchers);
        },
    }
});
