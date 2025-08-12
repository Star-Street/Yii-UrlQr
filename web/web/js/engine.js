$(document).ready(function () {
    $('#urlForm').on('submit', function (e) {
        e.preventDefault();

        let url = $('#urlInput').val().trim();

        if (!isValidUrl(url)) {
            showError('Please enter a valid URL (e.g. https://example.com/)');
            return;
        }

        toggleLoading(true);

        $.ajax({
            url: '/short-link/create',
            type: 'POST',
            dataType: 'json',
            data: {
                original_url: url,
                _csrf: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showResult(response.qrCode, response.shortUrl);
                } else {
                    showError(response.error || 'Unknown error occurred...');
                }
            },
            error: function(xhr) {
                showError(xhr.statusText || 'Server error...');
            },
            complete: function() {
                toggleLoading(false);
            }
        });
    });

    function toggleLoading(show) {
        if(show) {
            $('#spinner').removeClass('d-none');
            $('#btnText').text('Processing...');
            $('#submitBtn').prop('disabled', true);
        } else {
            $('#spinner').addClass('d-none');
            $('#btnText').text('OK');
            $('#submitBtn').prop('disabled', false);
        }
    }

    function isValidUrl(string) {
        try {
            new URL(string);
            return true;
        } catch (_) {
            return false;
        }
    }

    function showResult(qrCode, shortUrl) {
        $('#qrCodeImage').attr('src', qrCode);
        $('#shortUrl').val(shortUrl);
        $('#resultContainer').removeClass('d-none');
    }

    function showError(message) {
        if (typeof showError.timeout !== 'undefined') {
            clearTimeout(showError.timeout);
        }

        const $errorAlert = $('#errorAlert');
        $errorAlert.text(message).removeClass('d-none');

        showError.timeout = setTimeout(function() {
            $errorAlert.addClass('d-none');
        }, 3000);
    }

    $('#copyBtn').on('click', function() {
        var copyText = $('#shortUrl');
        copyText.select();
        document.execCommand('copy');

        var btn = $(this);
        btn.text('Copied!');
        setTimeout(function() {
            btn.text('Copy URL');
        }, 2000);
    });
});