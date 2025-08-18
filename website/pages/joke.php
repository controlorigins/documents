<?php 

declare(strict_types=1);

require_once __DIR__ . '/../includes/docs.php'; ?>
<div class="card shadow-sm fade-in">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h2 class="mb-0"><i class="bi bi-emoji-laughing me-2"></i>Random Jokes</h2>
            <p class="text-light mb-0">Enjoy some humor from an external API</p>
        </div>
        <div>
            <a href="<?= doc_pretty_url('ChatGPT/Sessions/Create PHP Joke Page'); ?>" class="btn btn-light btn-sm">
                <i class="bi bi-info-circle me-1"></i> How this page was created
            </a>
            <a href="https://github.com/markhazleton/PHPDocSpark/blob/main/website/pages/joke.php" target="_blank" class="btn btn-light btn-sm">
                <i class="bi bi-code-slash me-1"></i> View Source
            </a>
        </div>
    </div>
    
    <div class="card-body">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card joke-card mb-4 border-primary">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-chat-square-quote me-2"></i>Today's Joke</h5>
                    </div>
                    <div class="card-body p-4">
                        <div id="joke-container" class="py-3">
                            <div class="d-flex justify-content-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <button onclick="fetchJoke()" class="btn btn-primary">
                            <i class="bi bi-arrow-repeat me-1"></i> Get Another Joke
                        </button>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-lightbulb me-2"></i>About This Feature</h5>
                        <p class="card-text">
                            This page demonstrates PHP's ability to fetch data from an external API and dynamically display the results. 
                            It uses AJAX to make asynchronous requests to a PHP script that interacts with the JokeAPI service.
                        </p>
                        <p class="card-text">
                            The jokes are fetched in real-time each time you click the button, showcasing how PHP can act as an 
                            intermediary between your website and external data sources.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card-footer">
        <div class="executive-summary">
            <h5><i class="bi bi-file-text me-2"></i>Implementation Details</h5>
            <div class="row">
                <div class="col-md-4">
                    <div class="alert alert-primary">
                        <h6><i class="bi bi-code-slash me-2"></i>PHP Backend</h6>
                        <p class="mb-0">Makes cURL requests to the JokeAPI and formats the response</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-success">
                        <h6><i class="bi bi-arrow-repeat me-2"></i>JavaScript/AJAX</h6>
                        <p class="mb-0">Handles asynchronous requests to the PHP backend</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-info">
                        <h6><i class="bi bi-bootstrap me-2"></i>Bootstrap UI</h6>
                        <p class="mb-0">Provides responsive, attractive styling for the joke display</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .joke-card {
        box-shadow: 0 6px 10px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
    }
    
    .joke-card:hover {
        transform: translateY(-5px);
    }
    
    #joke-container {
        min-height: 150px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .joke-content {
        font-size: 1.2rem;
    }
</style>