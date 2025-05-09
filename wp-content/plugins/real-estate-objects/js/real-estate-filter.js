/**
 * Real Estate Filter JavaScript
 */
(function($) {
    'use strict';

    // Initialize the filter when the document is ready
    $(document).ready(function() {
        var $form = $('#real-estate-filter-form');
        var $results = $('#real-estate-results');
        var $container = $results.find('.results-container');
        var $pagination = $results.find('.results-pagination');
        var $loading = $results.find('.results-loading');
        var currentPage = 1;
        var currentSort = 'ecology-high'; // Default sort
        var currentView = 'blocks'; // Default view

        // Set initial active state for view buttons
        setViewButtonState();

        // Submit form handler
        $form.on('submit', function(e) {
            e.preventDefault();
            currentPage = 1;
            performSearch();
        });

        // Reset form handler
        $form.on('reset', function() {
            setTimeout(function() {
                currentPage = 1;
                performSearch();
            }, 10);
        });

        // Pagination click handler
        $pagination.on('click', 'a.page-numbers', function(e) {
            e.preventDefault();
            currentPage = parseInt($(this).data('page'), 10);
            performSearch();

            // Scroll to results
            $('html, body').animate({
                scrollTop: $results.offset().top - 50
            }, 500);
        });

        // Sort select handler
        $(document).on('change', '.sort-selector', function(e) {
            currentSort = $(this).val();
            currentPage = 1;
            performSearch();
        });

        // View option click handler
        $(document).on('click', '.view-option', function(e) {
            e.preventDefault();
            var $this = $(this);

            // Skip if already active
            if ($this.data('view-active') === true || $this.data('view-active') === 'true') {
                return;
            }

            // Get the view type
            currentView = $this.data('view');

            // Update active state
            setViewButtonState();

            // Re-render results with the new view
            if ($container.data('results')) {
                displayResults($container.data('results'));
            }
        });

        // Function to set view button state based on currentView
        function setViewButtonState() {
            $('.view-option').each(function() {
                var $btn = $(this);
                if ($btn.data('view') === currentView) {
                    $btn.addClass('btn-secondary').attr('data-view-active', 'true');
                } else {
                    $btn.removeClass('btn-secondary').removeAttr('data-view-active');
                }
            });
        }

        // Function to perform the search
        function performSearch() {
            // Show loading indicator
            $loading.show();
            $container.empty();
            $pagination.empty();

            // Get form data
            var formData = $form.serializeArray();

            // Add current page
            formData.push({
                name: 'page',
                value: currentPage
            });

            // Add sort option
            formData.push({
                name: 'sort',
                value: currentSort
            });

            // Add action and nonce
            formData.push({
                name: 'action',
                value: 'real_estate_filter'
            });

            formData.push({
                name: 'nonce',
                value: real_estate_filter.nonce
            });

            // Send AJAX request
            $.ajax({
                url: real_estate_filter.ajax_url,
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Store the results for reuse with view switching
                        $container.data('results', response.data.results);
                        displayResults(response.data.results);
                        displayPagination(response.data.pagination);
                    } else {
                        $container.html('<div class="no-results">Помилка: ' + response.data + '</div>');
                    }
                },
                error: function() {
                    $container.html('<div class="no-results">Помилка: не вдалося завантажити результати.</div>');
                },
                complete: function() {
                    $loading.hide();
                }
            });
        }

        // Function to display results
        function displayResults(results) {
            if (results.length === 0) {
                $container.html('<div class="no-results">Не знайдено об\'єктів за вашими критеріями.</div>');
                return;
            }

            var html = '';

            // Different layouts based on view
            if (currentView === 'blocks') {
                html = '<div class="row results-grid">';

                $.each(results, function(index, item) {
                    var buildingType = '';
                    switch (item.building_type) {
                        case 'panel':
                            buildingType = 'Панель';
                            break;
                        case 'brick':
                            buildingType = 'Цегла';
                            break;
                        case 'foam_block':
                            buildingType = 'Піноблок';
                            break;
                    }

                    html += '<div class="col-md-6 col-lg-4 mb-4">';
                    html += '<div class="card h-100">';

                    // Card image
                    if (item.image_url) {
                        html += '<a href="' + item.link + '">';
                        html += '<img src="' + item.image_url + '" class="card-img-top" alt="' + item.title + '">';
                        html += '</a>';
                    }

                    // Card body
                    html += '<div class="card-body">';
                    html += '<h5 class="card-title"><a href="' + item.link + '">' + item.title + '</a></h5>';

                    html += '<div class="card-meta">';
                    if (item.building_name) {
                        html += '<p><strong>Назва:</strong> ' + item.building_name + '</p>';
                    }
                    if (item.floors) {
                        html += '<p><strong>Поверхів:</strong> ' + item.floors + '</p>';
                    }
                    if (buildingType) {
                        html += '<p><strong>Тип:</strong> ' + buildingType + '</p>';
                    }
                    if (item.eco_rating) {
                        html += '<p><strong>Екологічність:</strong> ' + item.eco_rating + ' / 5</p>';
                    }
                    if (item.price && item.price > 0) {
                        html += '<p><strong>Ціна:</strong> ' + item.price + ' грн</p>';
                    }
                    html += '</div>';

                    html += '<div class="card-text">' + item.excerpt + '</div>';
                    html += '</div>';

                    // Card footer
                    html += '<div class="card-footer">';
                    html += '<a href="' + item.link + '" class="btn btn-primary">Детальніше</a>';
                    html += '</div>';

                    html += '</div>'; // End card
                    html += '</div>'; // End column
                });

                html += '</div>'; // End row
            } else {
                // List view
                html = '<div class="results-list">';

                $.each(results, function(index, item) {
                    var buildingType = '';
                    switch (item.building_type) {
                        case 'panel':
                            buildingType = 'Панель';
                            break;
                        case 'brick':
                            buildingType = 'Цегла';
                            break;
                        case 'foam_block':
                            buildingType = 'Піноблок';
                            break;
                    }

                    html += '<div class="result-item">';

                    // Image
                    html += '<div class="result-image">';
                    if (item.image_url) {
                        html += '<img src="' + item.image_url + '" alt="' + item.title + '">';
                    } else {
                        html += '<div class="no-image">Зображення відсутнє</div>';
                    }
                    html += '</div>';

                    // Content
                    html += '<div class="result-content">';
                    html += '<h3><a href="' + item.link + '">' + item.title + '</a></h3>';

                    html += '<div class="result-meta">';
                    if (item.building_name) {
                        html += '<span class="meta-item"><strong>Назва:</strong> ' + item.building_name + '</span>';
                    }
                    if (item.floors) {
                        html += '<span class="meta-item"><strong>Поверхів:</strong> ' + item.floors + '</span>';
                    }
                    if (buildingType) {
                        html += '<span class="meta-item"><strong>Тип:</strong> ' + buildingType + '</span>';
                    }
                    if (item.eco_rating) {
                        html += '<span class="meta-item"><strong>Екологічність:</strong> ' + item.eco_rating + ' / 5</span>';
                    }
                    if (item.price && item.price > 0) {
                        html += '<span class="meta-item"><strong>Ціна:</strong> ' + item.price + ' грн</span>';
                    }
                    html += '</div>';

                    html += '<div class="result-excerpt">' + item.excerpt + '</div>';
                    html += '<a href="' + item.link + '" class="view-details">Детальніше</a>';
                    html += '</div>';

                    html += '</div>';
                });

                html += '</div>';
            }

            $container.html(html);
        }

        // Function to display pagination
        function displayPagination(pagination) {
            if (pagination.total <= 1) {
                return;
            }

            var html = '<div class="pagination">';

            // Previous page
            if (pagination.current > 1) {
                html += '<a href="#" class="page-numbers prev" data-page="' + (pagination.current - 1) + '">« Попередня</a>';
            }

            // Page numbers
            for (var i = 1; i <= pagination.total; i++) {
                if (i === pagination.current) {
                    html += '<span class="page-numbers current">' + i + '</span>';
                } else {
                    html += '<a href="#" class="page-numbers" data-page="' + i + '">' + i + '</a>';
                }
            }

            // Next page
            if (pagination.current < pagination.total) {
                html += '<a href="#" class="page-numbers next" data-page="' + (pagination.current + 1) + '">Наступна »</a>';
            }

            html += '</div>';

            $pagination.html(html);
        }

        // Initial search
        performSearch();
    });

})(jQuery);
