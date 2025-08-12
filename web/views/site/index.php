<?php

/** @var yii\web\View $this */

$this->title = 'Yii-Reduce QR Code';
?>
<div class="site-index">

    <div class="jumbotron text-center bg-transparent mt-5 mb-5">
        <h1 class="display-4">Yii-Reduce QR Code</h1>

        <p class="lead">Simple project on Yii 2 - link shortener + QR generation images.</p>
    </div>

    <div class="body-content">

        <!-- Typing form -->
        <div class="row justify-content-center mb-5">
            <form id="urlForm" class="col-lg-8 col-md-10">

                <div class="row g-2">

                    <!-- Input data -->
                    <div class="col-md-8 col-12">
                        <div class="form-floating">
                            <input id="urlInput" type="url" class="form-control"
                                   placeholder="https://example.com" required>
                            <label for="urlInput">Enter your URL</label>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="col-md-4 col-12 d-flex">
                        <button id="submitBtn" class="btn btn-primary w-100 py-3" type="submit">
                            <span id="spinner" class="spinner-border spinner-border-sm d-none"></span>
                            <span id="btnText">OK</span>
                        </button>
                    </div>

                    <!-- Messages error -->
                    <div id="errorAlert" class="alert alert-danger d-none"></div>

                </div>

            </form>
        </div>

        <!-- Results -->
        <div id="resultContainer" class="row justify-content-center d-none">
            <div class="col-lg-6 col-md-8 text-center">

                <!-- QR-code -->
                <div id="qrCodeContainer" class="mb-4">
                    <img id="qrCodeImage" src="" class="img-fluid border p-2"
                         style="max-width: 350px;" alt="QR Code">
                </div>

                <!-- Short link -->
                <div class="input-group mb-3">
                    <input id="shortUrl" type="text" class="form-control" readonly>
                    <button id="copyBtn" class="btn btn-outline-secondary" type="button">
                        <span>Copy URL</span>
                    </button>
                </div>

            </div>

            <div class="col-lg-6 col-md-8 text-center">

                <p class="text-secondary">Click on the short link field to follow the link (a new tab will open).</p>

            </div>
        </div>
    </div>
</div>