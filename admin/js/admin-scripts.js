jQuery(document).ready(function ($) {

    // ===== Tab Switching (Header) =====
    $('.d5tm-htab').on('click', function () {
        var target = $(this).data('target');
        $('.d5tm-htab').removeClass('active');
        $(this).addClass('active');
        $('.d5tm-tab').removeClass('active');
        $('#' + target).addClass('active');
    });

    // ===== Filter Logic =====
    var activeFilterType = 'cat';
    var activeFilterVal  = 'all';
    var searchTerm       = '';

    function applyFilters() {
        var visible = 0;
        $('.d5tm-card').each(function () {
            var cardSearch   = $(this).data('search') || '';
            var categoryList = $(this).data('category') || '';
            var tagList      = $(this).data('tag') || '';

            var matchSearch = !searchTerm || cardSearch.indexOf(searchTerm) !== -1;
            
            var matchFilter = true;
            if (activeFilterVal !== 'all') {
                if (activeFilterType === 'cat') {
                    matchFilter = (' ' + categoryList + ' ').indexOf(' ' + activeFilterVal + ' ') !== -1;
                } else if (activeFilterType === 'tag') {
                    matchFilter = (' ' + tagList + ' ').indexOf(' ' + activeFilterVal + ' ') !== -1;
                }
            }

            if (matchSearch && matchFilter) {
                $(this).show();
                visible++;
            } else {
                $(this).hide();
            }
        });
        $('#d5tm-count-label').text(visible + ' result' + (visible !== 1 ? 's' : ''));
    }

    // Search
    $('#d5tm-search-input').on('input keyup', function () {
        searchTerm = $(this).val().trim().toLowerCase();
        applyFilters();
    });

    // Sidebar navigation
    $(document).on('click', '.d5tm-sidebar-link[data-filter-type]', function () {
        $('.d5tm-sidebar-link').removeClass('active');
        $(this).addClass('active');
        activeFilterType = $(this).data('filter-type');
        activeFilterVal  = $(this).data('filter-val');
        applyFilters();
    });

    // ===== Toolbar Select Dropdowns (Sort & Grid View) =====
    var $sortSelect     = $('#d5tm-sort-order'); // Matched to HTML id
    var $gridColsSelect = $('#d5tm-grid-cols');
    var $grid           = $('#d5tm-grid');

    // Sort Layouts — supports name A-Z, Z-A, date newest, date oldest
    $sortSelect.on('change', function () {
        var sortVal = $(this).val();
        var $cards  = $grid.children('.d5tm-card').toArray();

        $cards.sort(function (a, b) {
            if (sortVal === 'name_asc') {
                var na = ($(a).data('title') || '').toString().toLowerCase();
                var nb = ($(b).data('title') || '').toString().toLowerCase();
                return na.localeCompare(nb);
            } else if (sortVal === 'name_desc') {
                var na = ($(a).data('title') || '').toString().toLowerCase();
                var nb = ($(b).data('title') || '').toString().toLowerCase();
                return nb.localeCompare(na);
            } else if (sortVal === 'date_desc') {
                return parseInt($(b).data('date') || 0) - parseInt($(a).data('date') || 0);
            } else if (sortVal === 'date_asc') {
                return parseInt($(a).data('date') || 0) - parseInt($(b).data('date') || 0);
            }
            return 0;
        });

        $.each($cards, function (i, card) { $grid.append(card); });
    });

    // Grid Columns Layout
    $gridColsSelect.on('change', function () {
        var cols = $(this).val();
        $grid.removeClass('cols-1 cols-2 cols-3 cols-4 cols-5');
        if (cols !== 'auto') {
            $grid.addClass('cols-' + cols);
        }
        localStorage.setItem('d5tm_grid_cols', cols);
        
        // Re-scale any visible iframes based on new card widths
        $('.d5tm-card').each(function() {
            var $wrapper = $(this).find('.d5tm-iframe-wrapper.active');
            if ($wrapper.length) {
                applyIframeScale($wrapper);
            }
        });
    });

    // Restore saved grid preference on load
    var savedGrid = localStorage.getItem('d5tm_grid_cols') || 'auto';
    if (savedGrid !== 'auto') {
        $gridColsSelect.val(savedGrid).trigger('change');
    }

    // ===== Live Hover Preview (static top-of-layout snapshot) =====
    var hoverTimer = null;

    // Source viewport width for the iframe (simulates desktop)
    var IFRAME_SRC_W = 1200;
    var IFRAME_SRC_H = 700;  // only capture above-the-fold portion

    function applyIframeScale( $wrap, $iframe ) {
        // Use actual rendered card dimensions
        var wrapW = $wrap.outerWidth();
        var wrapH = $wrap.outerHeight();

        // Fallbacks if DOM hasn't painted yet
        if ( ! wrapW || wrapW < 10 ) wrapW = $wrap.parent().outerWidth() || 220;
        if ( ! wrapH || wrapH < 10 ) wrapH = Math.round( wrapW * 0.75 ); // 4:3 fallback

        // Scale factor: fit the 1200px-wide source into the card width
        var scale = wrapW / IFRAME_SRC_W;

        // Work out how tall the source needs to be so the scaled result
        // matches the card height EXACTLY â€” eliminates the white gap.
        var srcH = Math.ceil( wrapH / scale );

        // Negative margins collapse the space that transform:scale() leaves
        // (scale doesn't shrink the layout footprint, only visually shrinks).
        var marginRight  = -( IFRAME_SRC_W - wrapW );  // horizontal leftover
        var marginBottom = -( srcH - wrapH );           // vertical leftover

        $iframe.css({
            height          : srcH + 'px',
            transform       : 'scale(' + scale + ')',
            'margin-right'  : marginRight  + 'px',
            'margin-bottom' : marginBottom + 'px'
        });
        // Do NOT set wrap height â€” live-wrap has inset:0 so it fills the card naturally
    }

    $(document).on('mouseenter', '.d5tm-card', function () {
        var $card = $(this);
        var $wrap = $card.find('.d5tm-thumb-live-wrap');
        if ( ! $wrap.length ) return;           // card has a custom image â€” skip

        var previewUrl = $card.data('preview-url');
        if ( ! previewUrl ) return;

        // 250ms delay prevents loads on fast mouse-sweep
        hoverTimer = setTimeout(function () {
            var $iframe;

            if ( ! $wrap.find('iframe').length ) {
                $iframe = $('<iframe/>', {
                    src          : previewUrl,
                    frameborder  : '0',
                    scrolling    : 'no',
                    'aria-hidden': 'true'
                });
                $wrap.append( $iframe );
            } else {
                $iframe = $wrap.find('iframe');
            }

            // Apply the correct scale so iframe fills 100% of the card width
            applyIframeScale( $wrap, $iframe );

            $card.find('.d5tm-skel-static').css('opacity', '0');
            $wrap.addClass('active');
        }, 250);
    });

    $(document).on('mouseleave', '.d5tm-card', function () {
        clearTimeout(hoverTimer);
        var $card = $(this);
        var $wrap = $card.find('.d5tm-thumb-live-wrap');
        if ( ! $wrap.length ) return;

        $wrap.removeClass('active');
        $card.find('.d5tm-skel-static').css('opacity', '1');

        // Destroy iframe after transition to free memory
        setTimeout(function () {
            if ( ! $wrap.hasClass('active') ) {
                $wrap.find('iframe').remove();
            }
        }, 320);
    });

    // ===== Import: Drag & Drop =====
    var $dropzone  = $('#d5tm-dropzone');
    var $fileInput = $('#d5tm-file-input');
    var $status    = $('#d5tm-upload-status');

    $('#d5tm-browse-btn').on('click', function (e) {
        e.preventDefault();
        $fileInput.trigger('click');
    });

    $dropzone.on('dragover', function (e) {
        e.preventDefault();
        $(this).addClass('dragover');
    }).on('dragleave drop', function (e) {
        e.preventDefault();
        $(this).removeClass('dragover');
        if (e.type === 'drop') {
            handleFiles(e.originalEvent.dataTransfer.files);
        }
    });

    $fileInput.on('change', function () {
        handleFiles(this.files);
    });

    function handleFiles(files) {
        if (!files || files.length === 0) return;
        
        let fileMap = {};
        let jsons = [];
        
        // Map files by base name
        Array.from(files).forEach(function(file) {
            let parts = file.name.split('.');
            let ext = parts.pop().toLowerCase();
            let base = parts.join('.');
            if (!fileMap[base]) fileMap[base] = {};
            fileMap[base][ext] = file;
            
            if (ext === 'json') jsons.push(base);
        });

        if (jsons.length === 0) {
            showStatus('Please upload at least one valid .json file.', 'error');
            return;
        }

        $('#d5tm-dashboard-grid').css('opacity', '0.5'); // dim grid during upload
        uploadSequentially(jsons, fileMap, 0);
    }

    function uploadSequentially(jsons, fileMap, index) {
        if (index >= jsons.length) {
            showStatus('All imports finished! Reloading...', 'success');
            $fileInput.val('');
            setTimeout(function () { location.reload(); }, 1500);
            return;
        }
        
        let base = jsons[index];
        let jsonFile = fileMap[base]['json'];
        let imgFile = fileMap[base]['jpg'] || fileMap[base]['png'] || fileMap[base]['jpeg'] || fileMap[base]['webp'];
        
        var formData = new FormData();
        formData.append('action', 'd5tm_import_layout');
        formData.append('nonce', d5tm_ajax.nonce);
        formData.append('file', jsonFile);
        if (imgFile) formData.append('thumbnail', imgFile);

        showStatus('Uploading ' + (index + 1) + ' of ' + jsons.length + ': ' + base + '...', '');

        $.ajax({
            url: d5tm_ajax.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                uploadSequentially(jsons, fileMap, index + 1);
            },
            error: function () {
                uploadSequentially(jsons, fileMap, index + 1);
            }
        });
    }

    function showStatus(msg, type) {
        $status.text(msg).removeClass('success error');
        if (type) { $status.addClass(type); }
        else { $status.show(); }
    }

    // ===== Copy Layout Button =====
    $('.d5tm-copy-layout-btn').on('click', function (e) {
        e.preventDefault();
        var $btn = $(this);
        var layoutId = $btn.data('id');
        var originalText = $btn.html();

        // Loading state
        $btn.html('<span class="dashicons dashicons-update dashicons-update-spin"></span> Copying...');

        $.ajax({
            url: d5tm_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'd5tm_get_layout_data',
                nonce: d5tm_ajax.nonce,
                layout_id: layoutId
            },
            success: function (response) {
                if (response.success) {
                    var dataToCopy = response.data.layout_data;

                    // Fallback cross-page mechanism: Write to standard Divi local storage keys
                    try {
                        localStorage.setItem('divi_clipboard', dataToCopy);
                        localStorage.setItem('et_pb_templates_clipboard', dataToCopy);
                        localStorage.setItem('et_pb_recently_copied_module', dataToCopy);
                    } catch(err) {}

                    // Write strict plain text string to OS Clipboard
                    if (navigator.clipboard) {
                        navigator.clipboard.writeText(dataToCopy).then(function () {
                            showCopiedState($btn, originalText);
                        }).catch(function () {
                            fallbackCopyTextToClipboard(dataToCopy, $btn, originalText);
                        });
                    } else {
                        fallbackCopyTextToClipboard(dataToCopy, $btn, originalText);
                    }
                } else {
                    $btn.html('<span class="dashicons dashicons-warning"></span> Error');
                    setTimeout(function () { $btn.html(originalText); }, 2000);
                }
            },
            error: function () {
                $btn.html('<span class="dashicons dashicons-warning"></span> Error');
                setTimeout(function () { $btn.html(originalText); }, 2000);
            }
        });
    });

    function fallbackCopyTextToClipboard(text, $btn, originalText) {
        var textArea = document.createElement("textarea");
        textArea.value = text;
        
        // Avoid scrolling to bottom
        textArea.style.top = "0";
        textArea.style.left = "0";
        textArea.style.position = "fixed";

        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();

        try {
            document.execCommand('copy');
            showCopiedState($btn, originalText);
        } catch (err) {
            $btn.html('<span class="dashicons dashicons-warning"></span> Failed');
            setTimeout(function () { $btn.html(originalText); }, 2000);
        }

        document.body.removeChild(textArea);
    }

    function showCopiedState($btn, originalHtml) {
        $btn.html('<span class="dashicons dashicons-saved"></span> Copied!');
        $btn.addClass('copied');
        setTimeout(function () { 
            $btn.html(originalHtml);
            $btn.removeClass('copied');
        }, 2000);
    }

    // ===== Light/Dark Theme Toggle =====
    var $themeToggleBtn = $('#d5tm-theme-toggle');
    var $wrapContainer  = $('.d5tm-wrap');

    // Default to dark, so if the user chose dark previously, add the class.
    var savedTheme = localStorage.getItem('d5tm_theme') || 'light';
    
    if (savedTheme === 'dark') {
        $wrapContainer.addClass('d5tm-theme-dark');
        $themeToggleBtn.find('i').attr('class', 'bi bi-moon-stars');
    }

    $themeToggleBtn.on('click', function() {
        var $icon = $(this).find('i');
        if ($wrapContainer.hasClass('d5tm-theme-dark')) {
            $wrapContainer.removeClass('d5tm-theme-dark');
            localStorage.setItem('d5tm_theme', 'light');
            $icon.attr('class', 'bi bi-sun');
        } else {
            $wrapContainer.addClass('d5tm-theme-dark');
            localStorage.setItem('d5tm_theme', 'dark');
            $icon.attr('class', 'bi bi-moon-stars');
        }
    });

    // ===== Quick Edit Modal & WP Media Uploader =====
    var $modalOverlay = $('#d5tm-modal-overlay');
    var $qeForm       = $('#d5tm-quick-edit-form');
    var $statusMsg    = $('#d5tm-qe-status-msg');
    var currentCard   = null; // To store reference to the card being edited
    var mediaFrame;

    // Open Modal
    $(document).on('click', '.d5tm-action-quick-edit', function(e) {
        e.preventDefault();
        e.stopPropagation(); // prevent card click
        
        $statusMsg.text('').removeClass('error');
        currentCard = $(this).closest('.d5tm-card');
        
        // Extract Data
        var layoutId     = currentCard.data('layout-id');
        var title        = currentCard.data('title');
        var status       = currentCard.data('status');
        var thumbId      = currentCard.data('thumbnail-id');
        var thumbUrl     = currentCard.data('thumbnail-url');
        var tagsRaw      = currentCard.data('tags-raw');
        
        // Ensure data-cats-json is a valid string, then parse
        var catsJsonStr = currentCard.attr('data-cats-json');
        var catsIds = [];
        if (catsJsonStr) {
            try {
                catsIds = JSON.parse(catsJsonStr);
            } catch(e) {
                console.error("Invalid cats json", catsJsonStr);
            }
        }
        
        // Populate Form
        $('#d5tm-qe-layout-id').val(layoutId);
        $('#d5tm-qe-title').val(title);
        $('#d5tm-qe-status').val(status);
        $('#d5tm-qe-tags').val(tagsRaw);
        
        // Checkboxes
        $('input[name="layout_categories[]"]').prop('checked', false);
        if (catsIds.length > 0) {
            catsIds.forEach(function(catId) {
                // Ensure the string literal properly wraps exactly inside double quotes for attribute selector
                $('input[name="layout_categories[]"][value="' + catId + '"]').prop('checked', true);
            });
        }
        
        // Thumbnail
        $('#d5tm-qe-thumbnail-id').val(thumbId);
        if (thumbId && thumbUrl) {
            $('#d5tm-qe-thumbnail-img').attr('src', thumbUrl).show();
            $('#d5tm-qe-placeholder').hide();
        } else {
            $('#d5tm-qe-thumbnail-img').attr('src', '').hide();
            $('#d5tm-qe-placeholder').show();
        }
        
        $modalOverlay.addClass('active');
    });

    // Close Modal
    $('#d5tm-modal-close-btn, #d5tm-modal-cancel-btn').on('click', function() {
        $modalOverlay.removeClass('active');
    });

    // Handle WP Media Uploader
    $('#d5tm-qe-set-thumb').on('click', function(e) {
        e.preventDefault();

        // If frame exists, open it.
        if (mediaFrame) {
            mediaFrame.open();
            return;
        }

        // Create a new media frame
        mediaFrame = wp.media({
            title: 'Select or Upload a Preview Image',
            button: { text: 'Use this image' },
            multiple: false
        });

        // When an image is selected
        mediaFrame.on('select', function() {
            var attachment = mediaFrame.state().get('selection').first().toJSON();
            $('#d5tm-qe-thumbnail-id').val(attachment.id);
            // Show the image
            var imgUrl = attachment.sizes && attachment.sizes.large ? attachment.sizes.large.url : attachment.url;
            $('#d5tm-qe-thumbnail-img').attr('src', imgUrl).show();
            $('#d5tm-qe-placeholder').hide();
        });

        mediaFrame.open();
    });

    // Remove Thumbnail
    $('#d5tm-qe-remove-thumb').on('click', function(e) {
        e.preventDefault();
        $('#d5tm-qe-thumbnail-id').val('-1'); // -1 flags deletion in PHP
        $('#d5tm-qe-thumbnail-img').attr('src', '').hide();
        $('#d5tm-qe-placeholder').show();
    });

    // Save Quick Edit via AJAX
    $('#d5tm-qe-save-btn').on('click', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var formData = $qeForm.serializeArray();
        
        formData.push({ name: 'action', value: 'd5tm_quick_edit_layout' });
        formData.push({ name: 'nonce', value: d5tm_ajax.nonce });

        $btn.text('Updating...').prop('disabled', true);
        $statusMsg.text('').removeClass('error');

        $.ajax({
            url: d5tm_ajax.ajax_url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    $statusMsg.text('Updated successfully!').css('color', 'var(--green)');
                    
                    // Update DOM (Card Title)
                    if (currentCard) {
                        var newTitle = $('#d5tm-qe-title').val();
                        currentCard.data('title', newTitle);
                        currentCard.find('.d5tm-card-name').text(newTitle);
                        
                        // Update Thumbnail locally
                        var thumbId = $('#d5tm-qe-thumbnail-id').val();
                        var thumbUrl = $('#d5tm-qe-thumbnail-img').attr('src');
                        currentCard.data('thumbnail-id', thumbId == '-1' ? '0' : thumbId);
                        
                        if (thumbId == '-1' || !thumbUrl) {
                            // Revert to placeholder HTML inside the card
                            currentCard.find('.d5tm-thumb').html(`
                                <div class="d5tm-thumb-empty">
                                    <i class="bi bi-image"></i>
                                    <span>No preview</span>
                                </div>
                                <div class="d5tm-thumb-overlay">
                                    ${currentCard.find('.d5tm-thumb-overlay').html()}
                                </div>
                            `);
                        } else {
                            // Show new image in card
                            currentCard.find('.d5tm-thumb').html(`
                                <img class="d5tm-thumb-img" src="${thumbUrl}" alt="Preview" loading="lazy">
                                <div class="d5tm-thumb-overlay">
                                    ${currentCard.find('.d5tm-thumb-overlay').html()}
                                </div>
                            `);
                        }
                    }

                    setTimeout(function() {
                        $modalOverlay.removeClass('active');
                        $btn.text('Update').prop('disabled', false);
                    }, 1000);
                } else {
                    $statusMsg.text(response.data.message || 'Error occurred').css('color', 'var(--red)');
                    $btn.text('Update').prop('disabled', false);
                }
            },
            error: function() {
                $statusMsg.text('AJAX Error occurred').css('color', 'var(--red)');
                $btn.text('Update').prop('disabled', false);
            }
        });
    });

    // ===== Live Preview Modal =====
    var $lpModalOverlay = $('#d5tm-live-preview-modal');
    var $lpIframe       = $('#d5tm-lp-iframe');
    var $lpIframeWrap   = $('.d5tm-lp-iframe-wrapper');
    var $lpTitle        = $('#d5tm-lp-title');

    // Open Live Preview
    $(document).on('click', '.d5tm-action-live-preview', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var $card     = $(this).closest('.d5tm-card');
        var previewUrl = $card.data('preview-url');
        var title      = $card.data('title');

        if (previewUrl) {
            $lpTitle.text('Preview: ' + title);
            
            // Setting src with a slight delay allows the modal transition to start smoothly
            setTimeout(function() {
                $lpIframe.attr('src', previewUrl);
            }, 100);

            $lpModalOverlay.addClass('active');

            // Reset to desktop view
            $('.d5tm-lp-device-btn').removeClass('active');
            $('.d5tm-lp-device-btn[data-device="desktop"]').addClass('active');
            $lpIframeWrap.removeClass('desktop tablet mobile').addClass('desktop');
        }
    });

    // Close Live Preview
    $('#d5tm-lp-close').on('click', function() {
        $lpModalOverlay.removeClass('active');
        
        // Clear iframe source after transition to stop background processes/videos
        setTimeout(function() {
            $lpIframe.attr('src', '');
        }, 300);
    });

    // Responsive Toggles
    $('.d5tm-lp-device-btn').on('click', function() {
        var device = $(this).data('device');
        
        // Update active class on buttons
        $('.d5tm-lp-device-btn').removeClass('active');
        $(this).addClass('active');
        
        // Update wrapper class for CSS transition max-width
        $lpIframeWrap.removeClass('desktop tablet mobile').addClass(device);
    });

    // Refresh Dashboard
    $('.d5tm-action-refresh').on('click', function(e) {
        e.preventDefault();
        var $icon = $(this).find('.dashicons');
        $icon.addClass('dashicons-update-spin');
        window.location.reload();
    });

    // ===== Action logic Helpers =====
    function triggerActionHelper(action, layoutId, confirmMsg, successCallback) {
        if (confirmMsg && !confirm(confirmMsg)) return;
        
        $.ajax({
            url: d5tm_ajax.ajax_url,
            type: 'POST',
            data: {
                action: action,
                nonce: d5tm_ajax.nonce,
                layout_id: layoutId
            },
            success: function(response) {
                if (response.success) {
                    if (successCallback) successCallback(response);
                } else {
                    alert('Error: ' + response.data.message);
                }
            },
            error: function() {
                alert('An error occurred while communicating with the server.');
            }
        });
    }

    // Move to Trash
    $(document).on('click', '.d5tm-action-trash', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var $card = $(this).closest('.d5tm-card');
        var layoutId = $card.data('layout-id');
        triggerActionHelper('d5tm_trash_layout', layoutId, 'Move this layout to the Trash?', function() {
            $card.fadeOut(300, function() { $(this).remove(); });
        });
    });

    // Restore
    $(document).on('click', '.d5tm-action-restore', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var $card = $(this).closest('.d5tm-card');
        var layoutId = $card.data('layout-id');
        triggerActionHelper('d5tm_restore_layout', layoutId, null, function() {
            $card.fadeOut(300, function() { $(this).remove(); });
        });
    });

    // Delete Permanently
    $(document).on('click', '.d5tm-action-delete', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var $card = $(this).closest('.d5tm-card');
        var layoutId = $card.data('layout-id');
        triggerActionHelper('d5tm_delete_layout', layoutId, 'WARNING: This will permanently delete the layout. Are you sure?', function() {
            $card.fadeOut(300, function() { $(this).remove(); });
        });
    });

    // Download Layout (JSON)
    $(document).on('click', '.d5tm-action-download', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var layoutId = $(this).closest('.d5tm-card').data('layout-id');
        var $icon = $(this).find('.dashicons');
        
        // Loading state
        $icon.removeClass('dashicons-download').addClass('dashicons-update dashicons-update-spin');
        
        $.ajax({
            url: d5tm_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'd5tm_download_layout',
                nonce: d5tm_ajax.nonce,
                layout_id: layoutId
            },
            success: function(response) {
                if (response.success) {
                    var dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(response.data.json);
                    var downloadAnchorNode = document.createElement('a');
                    downloadAnchorNode.setAttribute("href", dataStr);
                    downloadAnchorNode.setAttribute("download", response.data.filename);
                    document.body.appendChild(downloadAnchorNode); // required for firefox
                    downloadAnchorNode.click();
                    downloadAnchorNode.remove();
                } else {
                    alert('Error: ' + response.data.message);
                }
            },
            error: function() {
                alert('A network error occurred.');
            },
            complete: function() {
                $icon.removeClass('dashicons-update dashicons-update-spin').addClass('dashicons-download');
            }
        });
    });

    // ===== Feature Guide Modal handlers =====
    var $guideModal = $('#d5tm-guide-modal');
    
    $('#d5tm-help-trigger').on('click', function() {
        $guideModal.addClass('active');
    });

    $('#d5tm-guide-close-btn, #d5tm-guide-ok-btn').on('click', function() {
        $guideModal.removeClass('active');
    });

    $guideModal.on('click', function(e) {
        if ($(e.target).is($guideModal)) {
            $guideModal.removeClass('active');
        }
    });

    // ===== Global Tooltip System (Portal Implementation) =====
    var $globalTooltip = $('<div class="d5tm-global-tooltip"></div>').appendTo('body');
    
    $(document).on('mouseenter', '[data-tooltip]', function() {
        var $el = $(this);
        var text = $el.attr('data-tooltip');
        if (!text) return;

        $globalTooltip.text(text);
        
        var rect = this.getBoundingClientRect();
        var tipWidth = $globalTooltip.outerWidth();
        var tipHeight = $globalTooltip.outerHeight();
        
        // Default position: above (top)
        var top = rect.top - tipHeight - 12;
        var left = rect.left + (rect.width / 2) - (tipWidth / 2);
        var posClass = 'pos-top';

        // SMART FLIP: If too close to top of viewport, show below
        if (rect.top < tipHeight + 40) {
            top = rect.bottom + 12;
            posClass = 'pos-bottom';
        }

        $globalTooltip.css({
            top: top + 'px',
            left: left + 'px'
        }).removeClass('pos-top pos-bottom').addClass(posClass + ' active');
    });

    $(document).on('mouseleave', '[data-tooltip]', function() {
        $globalTooltip.removeClass('active');
    });

    // ===== Term Color Management =====
    $(document).on('change', '.d5tm-term-color-picker', function() {
        var $picker = $(this);
        var termId  = $picker.data('term-id');
        var color   = $picker.val();

        // Update Folder Icon Color Instantly
        $picker.closest('.d5tm-sidebar-item-wrap').find('i').css('color', color);

        $.ajax({
            url: d5tm_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'd5tm_update_term_color',
                nonce: d5tm_ajax.nonce,
                term_id: termId,
                color: color
            },
            success: function(response) {
                if (response.success) {
                    // Refresh if layout grid needs to reflect new skeleton colors
                    console.log('Term color updated successfully.');
                }
            }
        });
    });

    // ============================================================
    // v2.7.0 — UX Polish & Category Management
    // ============================================================

    // === [1] Search Highlighting ===
    function highlightMatches( term ) {
        $('.d5tm-card-name').each(function () {
            var $el = $(this);
            // Restore original text first
            var original = $el.data('original-text') || $el.text();
            $el.data('original-text', original);

            if (!term) {
                $el.html(original);
                return;
            }
            var escaped = term.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            var regex   = new RegExp('(' + escaped + ')', 'gi');
            var highlighted = original.replace(regex, '<mark class="d5tm-highlight">$1</mark>');
            $el.html(highlighted);
        });
    }

    // Hook into existing search
    $('#d5tm-search-input').on('input keyup', function () {
        var term = $(this).val().trim().toLowerCase();
        highlightMatches(term);
    });

    // === [2] Keyboard Shortcuts ===
    $(document).on('keydown', function (e) {
        var tag = document.activeElement.tagName.toLowerCase();
        if (tag === 'input' || tag === 'textarea' || tag === 'select') return;

        // '/' — Focus search
        if (e.key === '/') {
            e.preventDefault();
            var $search = $('#d5tm-search-input');
            if ($search.length) {
                $search.focus();
            }
        }

        // 'Escape' — Clear filters & search
        if (e.key === 'Escape') {
            $('#d5tm-search-input').val('').trigger('input');
            highlightMatches('');
            $('.d5tm-sidebar-link').removeClass('active');
            $('.d5tm-sidebar-link[data-filter-val="all"]').addClass('active');
            if (typeof applyFilters === 'function') {
                // Reset internal state
                searchTerm = '';
                activeFilterVal = 'all';
                activeFilterType = 'cat';
                applyFilters();
            }
        }
    });

    // === [3] Copy Layout ID ===
    $(document).on('click', '.d5tm-copy-id-btn', function () {
        var $btn = $(this);
        var id   = $btn.data('id');

        if (!navigator.clipboard) {
            // Fallback for older browsers
            var $temp = $('<input>');
            $('body').append($temp);
            $temp.val(id).select();
            document.execCommand('copy');
            $temp.remove();
        } else {
            navigator.clipboard.writeText(String(id));
        }

        // Visual feedback
        $btn.addClass('copied');
        var $icon = $btn.find('i');
        $icon.removeClass('bi-clipboard').addClass('bi-clipboard-check');
        setTimeout(function () {
            $btn.removeClass('copied');
            $icon.removeClass('bi-clipboard-check').addClass('bi-clipboard');
        }, 1800);
    });

    // === [4] Delete Empty Categories (Settings Page) ===
    $(document).on('click', '#d5tm-delete-empty-cats', function () {
        var confirmed = confirm(
            'Are you sure you want to permanently delete ALL empty categories and tags?\n\nThis action cannot be undone.'
        );
        if (!confirmed) return;

        var $btn    = $(this);
        var $result = $('#d5tm-delete-cats-result');

        $btn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Deleting...');

        $.ajax({
            url:  d5tm_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'd5tm_delete_empty_cats',
                nonce:  d5tm_ajax.nonce
            },
            success: function (response) {
                $btn.prop('disabled', false).html('<i class="bi bi-trash3"></i> Delete All Empty Categories &amp; Tags');
                if (response.success) {
                    var cls = response.data.count > 0 ? 'd5tm-result-success' : 'd5tm-result-info';
                    $result
                        .removeClass('d5tm-result-success d5tm-result-info')
                        .addClass(cls)
                        .html('<i class="bi bi-' + (response.data.count > 0 ? 'check-circle' : 'info-circle') + '"></i> ' + response.data.message)
                        .show();
                } else {
                    $result.addClass('d5tm-result-info').html('<i class="bi bi-x-circle"></i> ' + (response.data ? response.data.message : 'An error occurred.')).show();
                }
            },
            error: function () {
                $btn.prop('disabled', false).html('<i class="bi bi-trash3"></i> Delete All Empty Categories &amp; Tags');
                $result.addClass('d5tm-result-info').html('<i class="bi bi-x-circle"></i> Network error. Please try again.').show();
            }
        });
    });

    // ============================================================
    // v3.0.0 — Batch Editor Logic
    // ============================================================

    var selectedIds = [];
    var isSelecting = false;

    function updateBulkBar() {
        if (selectedIds.length > 0) {
            $('#d5tm-bulk-count').text(selectedIds.length);
            $('#d5tm-bulk-bar-wrap').addClass('active');
        } else {
            $('#d5tm-bulk-bar-wrap').removeClass('active');
        }
    }

    // Toggle Select Mode
    $(document).on('click', '.d5tm-bulk-select-toggle', function() {
        isSelecting = !isSelecting;
        var $btn = $(this);
        
        if (isSelecting) {
            $btn.addClass('active').html('<i class="bi bi-x-circle"></i> Cancel Select');
            $('.d5tm-card').addClass('selecting');
        } else {
            $btn.removeClass('active').html('<i class="bi bi-check2-square"></i> Select');
            $('.d5tm-card').removeClass('selecting selected');
            $('.d5tm-card-checkbox').prop('checked', false);
            selectedIds = [];
            updateBulkBar();
        }
    });

    // Handle Individual Card Selection
    $(document).on('change', '.d5tm-card-checkbox', function() {
        var id = $(this).val();
        var $card = $(this).closest('.d5tm-card');

        if ($(this).is(':checked')) {
            if (selectedIds.indexOf(id) === -1) selectedIds.push(id);
            $card.addClass('selected');
        } else {
            selectedIds = selectedIds.filter(function(i) { return i !== id; });
            $card.removeClass('selected');
        }
        updateBulkBar();
    });

    // Bulk Dropdown Toggle
    $(document).on('click', '.d5tm-bulk-dropdown-trigger', function(e) {
        e.stopPropagation();
        $('.d5tm-bulk-dropdown').toggleClass('active');
    });

    $(document).click(function() {
        $('.d5tm-bulk-dropdown').removeClass('active');
    });

    // Bulk Cancel Btn
    $(document).on('click', '.d5tm-bulk-cancel', function() {
        $('.d5tm-bulk-select-toggle').trigger('click');
    });

    // --- Bulk AJAX Actions ---

    function runBulkAction(action, extraData, confirmMsg) {
        if (confirmMsg && !confirm(confirmMsg)) return;

        var $bar = $('.d5tm-bulk-bar');
        var originalContent = $bar.html();
        $bar.html('<div style="width:100%; text-align:center;"><i class="bi bi-arrow-repeat d5tm-spin"></i> Processing ' + selectedIds.length + ' items...</div>');

        var data = {
            action: action,
            ids: selectedIds,
            nonce: d5tm_ajax.nonce
        };
        $.extend(data, extraData);

        $.ajax({
            url: d5tm_ajax.ajax_url,
            type: 'POST',
            data: data,
            success: function(response) {
                if (response.success) {
                    location.reload(); // Simplest way to refresh state
                } else {
                    alert(response.data.message || 'Error executing bulk action.');
                    $bar.html(originalContent);
                    updateBulkBar();
                }
            },
            error: function() {
                alert('Network error.');
                $bar.html(originalContent);
                updateBulkBar();
            }
        });
    }

    // Assign Category
    $(document).on('click', '.d5tm-bulk-assign-cat', function() {
        var termId = $(this).data('cat-id');
        runBulkAction('d5tm_batch_update', { term_id: termId, tax: 'layout_category' });
    });

    // Bulk Trash
    $(document).on('click', '.d5tm-bulk-trash', function() {
        runBulkAction('d5tm_batch_trash', {}, 'Are you sure you want to move ' + selectedIds.length + ' items to trash?');
    });

    // --- JSON View ---
    $(document).on('click', '.d5tm-json-btn', function() {
        var id = $(this).data('id');
        var $modal = $('#d5tm-json-modal');
        var $viewer = $('#d5tm-json-viewer');

        $viewer.text('Loading JSON structure...');
        $modal.fadeIn(200).addClass('active');

        $.ajax({
            url: d5tm_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'd5tm_get_layout_data',
                layout_id: id,
                nonce: d5tm_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    try {
                        var content = response.data.layout_data;
                        $viewer.text(content);
                    } catch(e) {
                        $viewer.text(response.data.layout_data);
                    }
                } else {
                    $viewer.text('Error: ' + response.data.message);
                }
            }
        });
    });

    $('.d5tm-json-copy-btn').on('click', function() {
        var json = $('#d5tm-json-viewer').text();
        navigator.clipboard.writeText(json);
        var $btn = $(this);
        var original = $btn.text();
        $btn.text('Copied!');
        setTimeout(function() { $btn.text(original); }, 2000);
    });

    $('#d5tm-json-close-btn, #d5tm-json-ok-btn').on('click', function() {
        $('#d5tm-json-modal').fadeOut(200).removeClass('active');
    });

    // Refresh Dashboard

    $('.d5tm-action-refresh').on('click', function(e) {
        e.preventDefault();
        var $icon = $(this).find('.dashicons');
        $icon.addClass('dashicons-update-spin');
        window.location.reload();
    });

});
