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

document.addEventListener('click', function(event){
    if (event.target.id === 'load-more-btn') {
        onLoadMore(event);
    }
})

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

    oc.ajax('onProductListingUpdate', {
        data: payload,
        query: payload,
        update: {
            'ajax/listing-catalog': selectors.listing
        },
    })
}

function onLoadMore(event) {
    event.preventDefault();

    oc.ajax('onProductListingUpdate', {
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

document.addEventListener('DOMContentLoaded', function() {

    document.querySelectorAll('.filter-search').forEach(searchInput => {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const filterGroup = this.dataset.filterGroup;

            document.querySelectorAll(`input[name="${filterGroup}[]"]`).forEach(checkbox => {
                const label = checkbox.nextElementSibling;
                const row = checkbox.closest('.filters-row');
                const value = checkbox.value.toLowerCase();
                const text = label.textContent.toLowerCase();

                if (value.includes(searchTerm)) {
                    row.classList.remove('hidden');
                } else if (text.includes(searchTerm)) {
                    row.classList.remove('hidden');
                } else {
                    row.classList.add('hidden');
                }
            });
        });
    });
});
