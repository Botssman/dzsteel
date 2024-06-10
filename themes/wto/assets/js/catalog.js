const selectors = {
    form: '#catalog-form',
    sort: '#sort',
    listing: '#listing',
    count: '#count',
    submit: '#filters-submit',
    loadMoreBtn: '#load-more-btn'
}

const $form = document.querySelector(selectors.form);
const $sort = document.querySelector(selectors.sort);
const $loadMoreBtn = document.querySelector(selectors.loadMoreBtn);

if ($form) {
    $form.addEventListener('submit', onCatalogFormSubmit);
    $form.addEventListener('change', onFiltersChange);
}

if ($sort) {
    $sort.addEventListener('change', onCatalogFormSubmit);
}

if ($loadMoreBtn) {
    $loadMoreBtn.addEventListener('click', onLoadMore);
}

function onFiltersChange() {
    let payload = getFormPayload(document.querySelector(selectors.form));

    oc.ajax('Catalog::onCount', {
        data: payload,
        update: {
            'ajax/listing-catalog-submit': selectors.submit
        },
    })
}

function onCatalogFormSubmit(event) {
    event.preventDefault();

    let payload = getFormPayload(document.querySelector(selectors.form));

    oc.ajax('Catalog::onProductListingUpdate', {
        data: payload,
        query: payload,
        update: {
            'ajax/listing-catalog': selectors.listing
        },
    })
}

function onLoadMore(event) {
    event.preventDefault();

    oc.ajax('Catalog::onProductListingUpdate', {
        data: {
            cursor: event.target.dataset.nextCursor
        },
        query: {
            cursor: event.target.dataset.nextCursor
        },
        update: {
            'ajax/listing-catalog': `@${selectors.listing}`,
            'ajax/listing-catalog-count': selectors.count,
        },
    })
}

function getFormPayload($form) {
    let payload = {};

    let formData = new FormData($form);

    formData.forEach((value, key) => {
        if (value !== null && value !== '') {
            if (key.endsWith('_min') || key.endsWith('_max')) {
                payload[key] = value.replace(/\s/g, '');
            } else {
                if (key.endsWith('[]')) {
                    let realKey = key.slice(0, -2); // Remove the '[]' part from the key
                    if (payload[realKey]) {
                        if (Array.isArray(payload[realKey])) {
                            payload[realKey].push(value);
                        } else {
                            payload[realKey] = [payload[realKey], value];
                        }
                    } else {
                        payload[realKey] = [value];
                    }
                } else {
                    payload[key] = value;
                }
            }
        }
    });

    return payload;
}
