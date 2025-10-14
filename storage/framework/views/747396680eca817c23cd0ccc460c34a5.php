<script>
document.addEventListener('DOMContentLoaded', function() {
    // Admin carrier search functionality (similar to customer but with admin prefix)
    const searchInput = document.getElementById('admin-carrier-search');
    const carrierIdInput = document.getElementById('admin-carrier-id');
    const dropdown = document.getElementById('admin-carrier-dropdown');
    const statusSpan = document.getElementById('admin-carrier-status');

    if (!searchInput) return; // Exit if elements don't exist

    let searchTimeout;
    let selectedCarrierId = carrierIdInput.value;
    let currentPage = 1;
    let isLoading = false;

    // Update status based on current state
    function updateStatus() {
        if (selectedCarrierId) {
            statusSpan.textContent = '✓';
            statusSpan.className = 'text-xs text-green-600';
        } else if (searchInput.value.trim()) {
            statusSpan.textContent = '+';
            statusSpan.className = 'text-xs text-blue-600';
            statusSpan.title = 'Will create new carrier';
        } else {
            statusSpan.textContent = '';
            statusSpan.className = 'text-xs';
        }
    }

    // Search carriers
    function searchCarriers(query, page = 1) {
        if (query.length < 2) {
            dropdown.classList.add('hidden');
            return;
        }

        if (isLoading) return;
        isLoading = true;

        fetch(`<?php echo e(route('api.carriers.search')); ?>?q=${encodeURIComponent(query)}&page=${page}`)
            .then(response => response.json())
            .then(data => {
                if (page === 1) {
                    populateDropdown(data, query);
                } else {
                    appendToDropdown(data, query);
                }
                currentPage = page;
                isLoading = false;
            })
            .catch(error => {
                console.error('Search failed:', error);
                dropdown.classList.add('hidden');
                isLoading = false;
            });
    }

    // Append more results to dropdown
    function appendToDropdown(data, query) {
        // Remove the "Load more" button
        const loadMoreButton = dropdown.querySelector('[onclick*="searchCarriers"]');
        if (loadMoreButton) {
            loadMoreButton.remove();
        }

        // Add new carriers
        data.carriers.forEach(carrier => {
            const item = document.createElement('div');
            item.className = 'px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100';
            item.innerHTML = `
                <div class="font-medium text-gray-900">${carrier.name}</div>
                <div class="text-xs text-gray-500">
                    ${carrier.is_active ? 'Active carrier' : 'Inactive carrier - will be reactivated'}
                </div>
            `;
            item.onclick = () => selectCarrier(carrier.id, carrier.name);
            dropdown.appendChild(item);
        });

        // Add "Load more" again if there are still more results
        if (data.has_more) {
            const loadMoreItem = document.createElement('div');
            loadMoreItem.className = 'px-3 py-2 hover:bg-gray-50 cursor-pointer border-b border-gray-200 bg-gray-25 text-center';
            loadMoreItem.innerHTML = `<div class="text-sm text-gray-600">📄 Load more carriers...</div>`;
            loadMoreItem.onclick = () => {
                loadMoreItem.innerHTML = '<div class="text-sm text-gray-600">⏳ Loading...</div>';
                searchCarriers(query, currentPage + 1);
            };
            dropdown.appendChild(loadMoreItem);
        }
    }

    // Populate dropdown with results
    function populateDropdown(data, query) {
        dropdown.innerHTML = '';

        // Show total results if more than displayed
        if (data.total > data.carriers.length) {
            const headerItem = document.createElement('div');
            headerItem.className = 'px-3 py-2 bg-gray-100 border-b border-gray-200 text-xs text-gray-600';
            headerItem.innerHTML = `Showing ${data.carriers.length} of ${data.total} carriers`;
            dropdown.appendChild(headerItem);
        }

        // Show existing carriers
        data.carriers.forEach(carrier => {
            const item = document.createElement('div');
            item.className = 'px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100';
            item.innerHTML = `
                <div class="font-medium text-gray-900">${carrier.name}</div>
                <div class="text-xs text-gray-500">
                    ${carrier.is_active ? 'Active carrier' : 'Inactive carrier - will be reactivated'}
                </div>
            `;
            item.onclick = () => selectCarrier(carrier.id, carrier.name);
            dropdown.appendChild(item);
        });

        // Add "Load more" option if there are more results
        if (data.has_more) {
            const loadMoreItem = document.createElement('div');
            loadMoreItem.className = 'px-3 py-2 hover:bg-gray-50 cursor-pointer border-b border-gray-200 bg-gray-25 text-center';
            loadMoreItem.innerHTML = `<div class="text-sm text-gray-600">📄 Load more carriers...</div>`;
            loadMoreItem.onclick = () => {
                loadMoreItem.innerHTML = '<div class="text-sm text-gray-600">⏳ Loading...</div>';
                searchCarriers(query, currentPage + 1);
            };
            dropdown.appendChild(loadMoreItem);
        }

        // Add "Create new" option if no exact match
        if (!data.exact_match && query.trim()) {
            const createItem = document.createElement('div');
            createItem.className = 'px-3 py-2 hover:bg-green-50 cursor-pointer border-t-2 border-green-200 bg-green-25';
            createItem.innerHTML = `
                <div class="font-medium text-green-800">➕ Create "${query}"</div>
                <div class="text-xs text-green-600">Add as new carrier and use immediately</div>
            `;
            createItem.onclick = () => quickCreateCarrier(query);
            dropdown.appendChild(createItem);
        }

        dropdown.classList.remove('hidden');
    }

    // Select existing carrier
    function selectCarrier(id, name) {
        selectedCarrierId = id;
        carrierIdInput.value = id;
        searchInput.value = name;
        dropdown.classList.add('hidden');
        updateStatus();
    }

    // Quick create carrier (immediate API call)
    function quickCreateCarrier(name) {
        // Show loading state
        const createButton = dropdown.querySelector('[onclick*="quickCreateCarrier"]');
        if (createButton) {
            createButton.innerHTML = `
                <div class="font-medium text-green-800">⏳ Creating "${name}"...</div>
                <div class="text-xs text-green-600">Please wait...</div>
            `;
        }

        fetch('<?php echo e(route('api.carriers.quick-create')); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ name: name })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Select the newly created carrier
                selectCarrier(data.carrier.id, data.carrier.name);

                // Show success message briefly
                statusSpan.textContent = '✓';
                statusSpan.className = 'text-xs text-green-600';
                statusSpan.title = data.message;
            } else {
                alert('Failed to create carrier. Please try again.');
                dropdown.classList.add('hidden');
            }
        })
        .catch(error => {
            console.error('Create failed:', error);
            alert('Failed to create carrier. Please try again.');
            dropdown.classList.add('hidden');
        });
    }

    // Create new carrier (fallback - no immediate API call)
    function createNewCarrier(name) {
        selectedCarrierId = null;
        carrierIdInput.value = '';
        searchInput.value = name;
        dropdown.classList.add('hidden');
        updateStatus();
    }

    // Search input handler
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();

        // Reset selection when typing
        selectedCarrierId = null;
        carrierIdInput.value = '';
        currentPage = 1; // Reset pagination

        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            searchCarriers(query, 1);
        }, 300);

        updateStatus();
    });

    // Hide dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });

    // Show dropdown on focus if there's content
    searchInput.addEventListener('focus', function() {
        if (this.value.length >= 2) {
            searchCarriers(this.value);
        }
    });

    // Initial status update
    updateStatus();

    // ============================================
    // Supplier Search & Autocomplete
    // ============================================
    const supplierSearchInput = document.getElementById('admin-supplier-search');
    const supplierIdInput = document.getElementById('admin-supplier-id');
    const supplierDropdown = document.getElementById('admin-supplier-dropdown');
    const supplierStatus = document.getElementById('admin-supplier-status');

    let selectedSupplierId = supplierIdInput.value || null;
    let supplierSearchTimeout;
    let supplierCurrentPage = 1;

    function updateSupplierStatus() {
        if (supplierSearchInput.value && selectedSupplierId) {
            supplierStatus.textContent = '✓';
            supplierStatus.classList.add('text-green-600');
            supplierStatus.classList.remove('text-yellow-600');
        } else if (supplierSearchInput.value) {
            supplierStatus.textContent = '⚠';
            supplierStatus.classList.add('text-yellow-600');
            supplierStatus.classList.remove('text-green-600');
        } else {
            supplierStatus.textContent = '';
            supplierStatus.classList.remove('text-green-600', 'text-yellow-600');
        }
    }

    function searchSuppliers(query, page = 1) {
        if (query.length < 2) {
            supplierDropdown.classList.add('hidden');
            return;
        }

        fetch(`<?php echo e(route('api.suppliers.search')); ?>?q=${encodeURIComponent(query)}&page=${page}`)
            .then(response => response.json())
            .then(data => {
                populateSupplierDropdown(data.suppliers, data.has_more, data.exact_match, query);
            })
            .catch(error => {
                console.error('Error searching suppliers:', error);
            });
    }

    function populateSupplierDropdown(suppliers, hasMore, exactMatch, query) {
        supplierDropdown.innerHTML = '';

        if (suppliers.length === 0) {
            const noResults = document.createElement('div');
            noResults.className = 'p-3 text-sm text-gray-500 text-center';
            noResults.textContent = 'No suppliers found';
            supplierDropdown.appendChild(noResults);
        } else {
            suppliers.forEach(supplier => {
                const item = document.createElement('div');
                item.className = 'p-3 hover:bg-gray-100 cursor-pointer border-b border-gray-200';

                let statusBadge = '';
                if (!supplier.is_active) {
                    statusBadge = '<span class="text-xs text-yellow-600 ml-2">(inactive)</span>';
                }

                item.innerHTML = `
                    <div class="font-medium">${supplier.name}${statusBadge}</div>
                `;
                item.onclick = () => selectSupplier(supplier.id, supplier.name);
                supplierDropdown.appendChild(item);
            });
        }

        // Add "Create new" option if no exact match
        if (!exactMatch && query.length >= 2) {
            const createItem = document.createElement('div');
            createItem.className = 'p-3 bg-green-50 hover:bg-green-100 cursor-pointer border-t-2 border-green-200';
            createItem.innerHTML = `
                <div class="font-medium text-green-800">➕ Create "${query}"</div>
                <div class="text-xs text-green-600">Click to add new supplier</div>
            `;
            createItem.onclick = () => quickCreateSupplier(query);
            supplierDropdown.appendChild(createItem);
        }

        supplierDropdown.classList.remove('hidden');
    }

    function selectSupplier(id, name) {
        selectedSupplierId = id;
        supplierIdInput.value = id;
        supplierSearchInput.value = name;
        supplierDropdown.classList.add('hidden');
        updateSupplierStatus();
    }

    function quickCreateSupplier(name) {
        const createButton = supplierDropdown.querySelector('[onclick*="quickCreateSupplier"]');
        if (createButton) {
            createButton.innerHTML = `
                <div class="font-medium text-green-800">⏳ Creating "${name}"...</div>
                <div class="text-xs text-green-600">Please wait...</div>
            `;
        }

        fetch('<?php echo e(route('api.suppliers.quick-create')); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ name: name })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                selectSupplier(data.supplier.id, data.supplier.name);
            } else {
                alert('Error creating supplier');
                supplierDropdown.classList.add('hidden');
            }
        })
        .catch(error => {
            console.error('Error creating supplier:', error);
            alert('Error creating supplier');
            supplierDropdown.classList.add('hidden');
        });
    }

    supplierSearchInput.addEventListener('input', function() {
        const query = this.value.trim();

        selectedSupplierId = null;
        supplierIdInput.value = '';
        supplierCurrentPage = 1;

        clearTimeout(supplierSearchTimeout);
        supplierSearchTimeout = setTimeout(() => {
            searchSuppliers(query, 1);
        }, 300);

        updateSupplierStatus();
    });

    document.addEventListener('click', function(e) {
        if (!supplierSearchInput.contains(e.target) && !supplierDropdown.contains(e.target)) {
            supplierDropdown.classList.add('hidden');
        }
    });

    supplierSearchInput.addEventListener('focus', function() {
        if (this.value.length >= 2) {
            searchSuppliers(this.value);
        }
    });

    updateSupplierStatus();

    // ============================================
    // Contact Name Autocomplete with Phone Lookup
    // ============================================
    const contactNameInput = document.getElementById('admin-contact-name-input');
    const contactPhoneInput = document.getElementById('admin-contact-phone-input');
    const contactDropdown = document.getElementById('admin-contact-dropdown');
    const contactStatus = document.getElementById('admin-contact-status');
    const contactSupplierInput = document.getElementById('admin-supplier-search');
    const haulierInput = document.getElementById('admin-carrier-search'); // Carrier field is now Haulier
    const slotSelect = document.querySelector('select[name="slot_id"]');

    if (contactNameInput) {
        let contactSearchTimeout;

        // Search contacts as user types
        contactNameInput.addEventListener('input', function() {
            const query = this.value.trim();

            clearTimeout(contactSearchTimeout);

            if (query.length < 2) {
                contactDropdown.classList.add('hidden');
                return;
            }

            contactStatus.textContent = '⏳';
            contactStatus.className = 'text-xs text-gray-400';

            contactSearchTimeout = setTimeout(() => {
                searchContacts(query);
            }, 300);
        });

        // Search contacts via API
        function searchContacts(query) {
            const depot_id = slotSelect?.value ? getDepotFromSlot(slotSelect.value) : null;
            const supplier = contactSupplierInput?.value || '';
            const haulier = haulierInput?.value || '';

            const params = new URLSearchParams({
                query: query,
                ...(depot_id && { depot_id }),
                ...(supplier && { supplier }),
                ...(haulier && { haulier })
            });

            fetch(`<?php echo e(route('api.contacts.search')); ?>?${params}`)
                .then(response => response.json())
                .then(contacts => {
                    populateContactDropdown(contacts);
                    contactStatus.textContent = '';
                })
                .catch(error => {
                    console.error('Contact search failed:', error);
                    contactDropdown.classList.add('hidden');
                    contactStatus.textContent = '';
                });
        }

        // Populate contact dropdown
        function populateContactDropdown(contacts) {
            contactDropdown.innerHTML = '';

            if (contacts.length === 0) {
                contactDropdown.classList.add('hidden');
                return;
            }

            contacts.forEach(contact => {
                const item = document.createElement('div');
                item.className = 'px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100';
                item.innerHTML = `
                    <div class="font-medium text-gray-900">${contact.name}</div>
                    <div class="text-xs text-gray-600">${contact.phone}</div>
                    ${contact.supplier || contact.haulier ? `
                        <div class="text-xs text-gray-500">
                            ${contact.supplier ? 'Supplier: ' + contact.supplier : ''}
                            ${contact.haulier ? ' Haulier: ' + contact.haulier : ''}
                        </div>
                    ` : ''}
                `;
                item.onclick = () => selectContact(contact);
                contactDropdown.appendChild(item);
            });

            contactDropdown.classList.remove('hidden');
        }

        // Select a contact from dropdown
        function selectContact(contact) {
            contactNameInput.value = contact.name;
            contactPhoneInput.value = contact.phone;

            // Optionally fill supplier/haulier if they're empty
            if (!contactSupplierInput.value && contact.supplier) {
                contactSupplierInput.value = contact.supplier;
            }
            if (!haulierInput.value && contact.haulier) {
                haulierInput.value = contact.haulier;
            }

            contactDropdown.classList.add('hidden');
            contactStatus.textContent = '✓';
            contactStatus.className = 'text-xs text-green-600';
        }

        // Lookup phone when contact name loses focus
        contactNameInput.addEventListener('blur', function() {
            setTimeout(() => {
                if (this.value.trim() && !contactPhoneInput.value) {
                    lookupContactPhone(this.value.trim());
                }
                contactDropdown.classList.add('hidden');
            }, 200);
        });

        // Lookup contact phone by name
        function lookupContactPhone(name) {
            const depot_id = slotSelect?.value ? getDepotFromSlot(slotSelect.value) : null;
            const supplier = contactSupplierInput?.value || '';
            const haulier = haulierInput?.value || '';

            const params = new URLSearchParams({
                name: name,
                ...(depot_id && { depot_id }),
                ...(supplier && { supplier }),
                ...(haulier && { haulier })
            });

            fetch(`<?php echo e(route('api.contacts.phone')); ?>?${params}`)
                .then(response => response.json())
                .then(data => {
                    if (data.phone) {
                        contactPhoneInput.value = data.phone;
                        contactStatus.textContent = '✓';
                        contactStatus.className = 'text-xs text-green-600';
                        contactStatus.title = 'Phone number found';
                    }
                })
                .catch(error => {
                    console.error('Phone lookup failed:', error);
                });
        }

        // Helper function to extract depot ID from slot select
        function getDepotFromSlot(slotId) {
            // This would need to be implemented based on your slot data structure
            // For now, we'll return null and rely on supplier/haulier filtering
            return null;
        }

        // Hide dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!contactNameInput.contains(e.target) && !contactDropdown.contains(e.target)) {
                contactDropdown.classList.add('hidden');
            }
        });
    }

    // Toggle PO Numbers section visibility based on customer selection
    const customerSelect = document.querySelector('select[name="customer_id"]');
    const poNumbersSection = document.getElementById('po-numbers-section');
    const customerNotice = document.getElementById('customer-selection-notice');

    if (customerSelect && poNumbersSection && customerNotice) {
        customerSelect.addEventListener('change', function() {
            if (this.value) {
                poNumbersSection.style.display = 'block';
                customerNotice.style.display = 'none';
            } else {
                poNumbersSection.style.display = 'none';
                customerNotice.style.display = 'block';
            }
        });
    }

    // Hide SKU fields on edit page if not required
    <?php if($booking->exists): ?>
    const poSectionContainerEdit = document.getElementById('po-section-container');
    if (poSectionContainerEdit) {
        const showSku = poSectionContainerEdit.getAttribute('data-show-sku');
        if (showSku === 'false') {
            // Hide SKU fields on edit page
            const skuFieldContainers = poSectionContainerEdit.querySelectorAll('[data-sku-field-container]');
            skuFieldContainers.forEach(field => {
                field.style.display = 'none';
                // Remove required attribute from hidden fields
                const inputs = field.querySelectorAll('input[required], select[required]');
                inputs.forEach(input => {
                    input.required = false;
                });
            });
        }
    }
    <?php endif; ?>

    // Dynamic Customer/Slot Configuration Handler
    <?php if(!$booking->exists): ?>
    const customerSelectNew = document.querySelector('select[name="customer_id"]');
    const slotSelectNew = document.querySelector('select[name="slot_id"]');
    const poSectionContainer = document.getElementById('po-section-container');
    const poSectionPlaceholder = document.getElementById('po-section-placeholder');
    const poSectionContent = document.getElementById('po-section-content');
    const poSectionHeader = document.getElementById('po-section-header');
    const poSectionTitle = document.getElementById('po-section-title');
    const poSectionMessage = document.getElementById('po-section-message');

    function updatePoSectionVisibility() {
        const customerId = customerSelectNew?.value;
        const slotId = slotSelectNew?.value;

        // Only fetch config if both customer and slot are selected
        if (!customerId || !slotId) {
            if (poSectionContainer) poSectionContainer.style.display = 'none';
            if (poSectionPlaceholder) poSectionPlaceholder.style.display = 'block';
            return;
        }

        // Fetch customer configuration
        fetch(`/api/customer-config?customer_id=${customerId}&slot_id=${slotId}`)
            .then(response => response.json())
            .then(data => {
                const config = data.config;
                console.log('Customer config:', config);

                // Hide placeholder
                if (poSectionPlaceholder) poSectionPlaceholder.style.display = 'none';

                // Show container and content
                if (poSectionContainer) poSectionContainer.style.display = 'block';
                if (poSectionContent) poSectionContent.style.display = 'block';

                // Update header styling and message based on requirement
                if (config.require_po_data) {
                    if (poSectionHeader) {
                        poSectionHeader.className = 'p-4 rounded-lg border mb-4 bg-green-50 border-green-200';
                    }
                    if (poSectionTitle) {
                        poSectionTitle.className = 'text-base font-semibold mb-2 text-green-900';
                        poSectionTitle.innerHTML = '📦 PO Numbers & Expected Quantities <span class="text-red-500">*</span>';
                    }
                    if (poSectionMessage) {
                        poSectionMessage.className = 'text-sm text-green-800';
                        poSectionMessage.textContent = 'ℹ️ At least one PO with product details is required to create this booking.';
                    }

                    // Store that PO is required for validation
                    if (poSectionContent) poSectionContent.setAttribute('data-po-required', 'true');
                } else {
                    if (poSectionHeader) {
                        poSectionHeader.className = 'p-4 rounded-lg border mb-4 bg-blue-50 border-blue-200';
                    }
                    if (poSectionTitle) {
                        poSectionTitle.className = 'text-base font-semibold mb-2 text-blue-900';
                        poSectionTitle.textContent = '📦 PO Numbers & Product Details';
                    }
                    if (poSectionMessage) {
                        poSectionMessage.className = 'text-sm text-blue-800';
                        poSectionMessage.textContent = 'ℹ️ PO numbers and product details are optional for this customer. You can add them now or later via the booking edit page.';
                    }

                    // Store that PO is optional
                    if (poSectionContent) poSectionContent.setAttribute('data-po-required', 'false');
                }

                // Trigger Alpine to initialize if not already done
                setTimeout(() => {
                    if (window.poNumbersManagerInstance && window.poNumbersManagerInstance.poNumbers.length === 0) {
                        window.poNumbersManagerInstance.addPoNumber();
                        window.poNumbersManagerInstance.addPoLine(0);
                    }
                }, 100);

                // Control SKU field visibility within the component
                if (config.sku_fields_enabled) {
                    // Show SKU fields
                    const skuFieldContainers = poSectionContainer.querySelectorAll('[data-sku-field-container]');
                    skuFieldContainers.forEach(field => {
                        field.style.display = 'block';
                        // Re-enable required attributes if needed
                        const inputs = field.querySelectorAll('input[data-was-required], select[data-was-required]');
                        inputs.forEach(input => {
                            input.required = true;
                            input.removeAttribute('data-was-required');
                        });
                    });
                } else {
                    // Hide SKU fields only (keep PO section visible)
                    const skuFieldContainers = poSectionContainer.querySelectorAll('[data-sku-field-container]');
                    skuFieldContainers.forEach(field => {
                        field.style.display = 'none';
                        // Remove required attribute from hidden fields to allow form submission
                        const inputs = field.querySelectorAll('input[required], select[required]');
                        inputs.forEach(input => {
                            input.setAttribute('data-was-required', 'true');
                            input.required = false;
                        });
                    });
                }
            })
            .catch(error => {
                console.error('Error fetching customer config:', error);
            });
    }

    // Listen for customer/slot changes
    if (customerSelectNew) {
        customerSelectNew.addEventListener('change', updatePoSectionVisibility);
    }
    if (slotSelectNew) {
        slotSelectNew.addEventListener('change', updatePoSectionVisibility);
    }
    <?php endif; ?>
});
</script>
<?php /**PATH /Users/londo/Herd/test/resources/views/admin/bookings/_form_scripts.blade.php ENDPATH**/ ?>