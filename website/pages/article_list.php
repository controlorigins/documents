<?php
require_once __DIR__ . '/../includes/docs.php';

// Load articles data from remote RSS feed
$rssUrl = 'https://markhazleton.com/rss.xml';
$articles = [];
$error = '';
$dataSource = ''; // Track data source for display

// Function to fetch RSS feed from remote URL
function fetchRssFromUrl($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL peer verification for local dev
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); // Disable SSL host verification for local dev
    curl_setopt($ch, CURLOPT_USERAGENT, 'PHPDocSpark/1.0');
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    if ($curlError) {
        return ['error' => 'cURL Error: ' . $curlError, 'data' => false];
    }
    
    if ($httpCode !== 200) {
        return ['error' => 'HTTP Error: ' . $httpCode, 'data' => false];
    }
    
    return ['error' => '', 'data' => $response];
}

// Function to parse RSS feed and extract articles
function parseRssFeed($xmlString) {
    $articles = [];
    
    try {
        $xml = new SimpleXMLElement($xmlString);
        
        foreach ($xml->channel->item as $item) {
            $pubDate = (string)$item->pubDate;
            $dateObj = DateTime::createFromFormat('D, d M Y H:i:s T', $pubDate);
            
            $articles[] = [
                'title' => (string)$item->title,
                'link' => (string)$item->link,
                'description' => (string)$item->description,
                'category' => (string)$item->category,
                'pubDate' => $pubDate,
                'pubDateFormatted' => $dateObj ? $dateObj->format('M j, Y') : 'Unknown',
                'pubDateSort' => $dateObj ? $dateObj->getTimestamp() : 0,
                'guid' => (string)$item->guid
            ];
        }
        
        // Sort articles by publication date (newest first)
        usort($articles, function($a, $b) {
            return $b['pubDateSort'] - $a['pubDateSort'];
        });
        
    } catch (Exception $e) {
        return ['error' => 'XML parsing error: ' . $e->getMessage(), 'articles' => []];
    }
    
    return ['error' => '', 'articles' => $articles];
}

// Try to fetch from remote RSS URL
$remoteResult = fetchRssFromUrl($rssUrl);

if ($remoteResult['data'] !== false) {
    $parseResult = parseRssFeed($remoteResult['data']);
    
    if (empty($parseResult['error'])) {
        $articles = $parseResult['articles'];
        $dataSource = 'Remote RSS';
    } else {
        $error = $parseResult['error'];
        $articles = [];
        $dataSource = 'Remote RSS (failed)';
    }
} else {
    $error = $remoteResult['error'];
    $dataSource = 'RSS Feed (unavailable)';
}

// Pagination setup
$articlesPerPage = 12; // Number of articles per page
$currentPage = isset($_GET['p']) ? max(1, intval($_GET['p'])) : 1;

// Handle filtering and searching
$filterCategory = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';

// Filter articles based on parameters
$filteredArticles = $articles;

if ($filterCategory !== 'all') {
    $filteredArticles = array_filter($filteredArticles, function($article) use ($filterCategory) {
        $category = !empty($article['category']) ? $article['category'] : 'Uncategorized';
        return $category === $filterCategory;
    });
}

if (!empty($searchTerm)) {
    $searchLower = strtolower($searchTerm);
    $filteredArticles = array_filter($filteredArticles, function($article) use ($searchLower) {
        $title = strtolower($article['title']);
        $description = strtolower($article['description']);
        $category = strtolower($article['category'] ?: 'Uncategorized');
        
        return strpos($title, $searchLower) !== false || 
               strpos($description, $searchLower) !== false ||
               strpos($category, $searchLower) !== false;
    });
}

// Re-index the filtered array
$filteredArticles = array_values($filteredArticles);

$totalArticles = count($filteredArticles);
$totalPages = ceil($totalArticles / $articlesPerPage);
$offset = ($currentPage - 1) * $articlesPerPage;

// Get articles for current page
$currentArticles = array_slice($filteredArticles, $offset, $articlesPerPage);

// Group ALL articles by category for filter buttons (not just filtered ones)
$groupedArticles = [];
$articleCategories = [];

foreach ($articles as $article) {
    $category = !empty($article['category']) ? $article['category'] : 'Uncategorized';
    
    if (!in_array($category, $articleCategories)) {
        $articleCategories[] = $category;
    }
    
    if (!isset($groupedArticles[$category])) {
        $groupedArticles[$category] = [];
    }
    
    $groupedArticles[$category][] = $article;
}

// Sort categories alphabetically
sort($articleCategories);
?>

<div class="card shadow-sm fade-in">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h2 class="mb-0"><i class="bi bi-journal-text me-2"></i>Articles List</h2>
            <p class="text-light mb-0">Explore technical articles and insights</p>
        </div>
        <div>
            <a href="<?= doc_pretty_url('ChatGPT/Sessions/Create PHP Project Table'); ?>" class="btn btn-light btn-sm">
                <i class="bi bi-info-circle me-1"></i> How this was created
            </a>
            <a href="https://github.com/markhazleton/PHPDocSpark/blob/main/website/pages/article_list.php" target="_blank" class="btn btn-light btn-sm">
                <i class="bi bi-code-slash me-1"></i> View Source
            </a>
        </div>
    </div>
    
    <div class="card-body">
        <?php if (!empty($articles)): ?>
        <!-- Article Filters -->
        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5><i class="bi bi-funnel me-2"></i>Filter Articles</h5>
                <div class="input-group" style="max-width: 300px;">
                    <form style="display: contents;" method="GET" action="">
                        <input type="hidden" name="page" value="article_list">
                        <?php if ($filterCategory !== 'all'): ?>
                        <input type="hidden" name="filter" value="<?php echo htmlspecialchars($filterCategory); ?>">
                        <?php endif; ?>
                        <input type="text" id="articleSearch" name="search" class="form-control" placeholder="Search articles..." value="<?php echo htmlspecialchars($searchTerm); ?>">
                        <button type="submit" class="btn btn-outline-secondary">
                            <i class="bi bi-search"></i>
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="btn-group flex-wrap" role="group">
                <button type="button" class="btn btn-outline-primary <?php echo ($filterCategory === 'all') ? 'active' : ''; ?>" data-filter="all">
                    All
                </button>
                <?php foreach ($articleCategories as $category): ?>
                <button type="button" class="btn btn-outline-primary <?php echo ($filterCategory === $category) ? 'active' : ''; ?>" data-filter="<?php echo htmlspecialchars($category); ?>">
                    <?php echo htmlspecialchars($category); ?>
                </button>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Articles Display -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-4" id="articlesContainer">
            <?php foreach ($currentArticles as $index => $article): ?>
            <div class="col article-item" data-category="<?php echo htmlspecialchars($article['category'] ?: 'Uncategorized'); ?>">
                <div class="card h-100 article-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge bg-secondary"><?php echo htmlspecialchars($article['category'] ?: 'Uncategorized'); ?></span>
                            <small class="text-muted"><?php echo htmlspecialchars($article['pubDateFormatted']); ?></small>
                        </div>
                        
                        <h5 class="card-title">
                            <?php echo htmlspecialchars($article['title']); ?>
                        </h5>
                        
                        <p class="card-text">
                            <?php echo htmlspecialchars($article['description']); ?>
                        </p>
                    </div>
                    <div class="card-footer">
                        <a href="<?php echo htmlspecialchars($article['link']); ?>" class="btn btn-primary w-100" target="_blank">
                            <i class="bi bi-box-arrow-up-right me-1"></i> Read Article
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Pagination Controls -->
        <?php if ($totalPages > 1): ?>
        <nav aria-label="Article pagination" class="mb-4">
            <ul class="pagination justify-content-center" id="articlePagination">
                <!-- Previous Page -->
                <?php if ($currentPage > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=article_list&p=<?php echo ($currentPage - 1); ?><?php echo ($filterCategory !== 'all') ? '&filter=' . urlencode($filterCategory) : ''; ?><?php echo !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : ''; ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <?php else: ?>
                <li class="page-item disabled">
                    <span class="page-link">&laquo;</span>
                </li>
                <?php endif; ?>
                
                <!-- Page Numbers -->
                <?php
                $startPage = max(1, $currentPage - 2);
                $endPage = min($totalPages, $currentPage + 2);
                
                // Show first page if not in range
                if ($startPage > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=article_list&p=1<?php echo ($filterCategory !== 'all') ? '&filter=' . urlencode($filterCategory) : ''; ?><?php echo !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : ''; ?>">1</a>
                </li>
                <?php if ($startPage > 2): ?>
                <li class="page-item disabled"><span class="page-link">...</span></li>
                <?php endif; ?>
                <?php endif; ?>
                
                <!-- Current range pages -->
                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                <li class="page-item <?php echo ($i === $currentPage) ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=article_list&p=<?php echo $i; ?><?php echo ($filterCategory !== 'all') ? '&filter=' . urlencode($filterCategory) : ''; ?><?php echo !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : ''; ?>"><?php echo $i; ?></a>
                </li>
                <?php endfor; ?>
                
                <!-- Show last page if not in range -->
                <?php if ($endPage < $totalPages): ?>
                <?php if ($endPage < $totalPages - 1): ?>
                <li class="page-item disabled"><span class="page-link">...</span></li>
                <?php endif; ?>
                <li class="page-item">
                    <a class="page-link" href="?page=article_list&p=<?php echo $totalPages; ?><?php echo ($filterCategory !== 'all') ? '&filter=' . urlencode($filterCategory) : ''; ?><?php echo !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : ''; ?>"><?php echo $totalPages; ?></a>
                </li>
                <?php endif; ?>
                
                <!-- Next Page -->
                <?php if ($currentPage < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=article_list&p=<?php echo ($currentPage + 1); ?><?php echo ($filterCategory !== 'all') ? '&filter=' . urlencode($filterCategory) : ''; ?><?php echo !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : ''; ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
                <?php else: ?>
                <li class="page-item disabled">
                    <span class="page-link">&raquo;</span>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
        
        <?php else: ?>
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle me-2"></i> 
            Unable to load articles data. 
            <?php if (!empty($error)): ?>
                <br><small class="text-muted">Error: <?php echo htmlspecialchars($error); ?></small>
            <?php endif; ?>
            Please try refreshing the page.
        </div>
        <?php endif; ?>
    </div>
    
    <div class="card-footer">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <i class="bi bi-journal-text me-1"></i> 
                <span id="articleCount" class="badge bg-primary rounded-pill"><?php echo count($currentArticles); ?></span> 
                of <span class="badge bg-secondary rounded-pill"><?php echo $totalArticles; ?></span> articles
                <?php if ($totalPages > 1): ?>
                <span class="text-muted ms-2">(Page <?php echo $currentPage; ?> of <?php echo $totalPages; ?>)</span>
                <?php endif; ?>
            </div>
            <div>
                <a href="https://markhazleton.com/rss.xml" target="_blank" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-rss me-1"></i> View RSS Feed
                </a>
            </div>
        </div>
        <div class="mt-2 text-center">
            <small class="text-muted">
                <i class="bi bi-<?php echo ($dataSource === 'Remote RSS') ? 'rss-fill' : 'exclamation-triangle'; ?> me-1"></i>
                Data source: 
                <span class="<?php echo ($dataSource === 'Remote RSS') ? 'text-success' : 'text-danger'; ?>">
                    <?php echo htmlspecialchars($dataSource); ?>
                </span>
                <?php if ($dataSource === 'Remote RSS'): ?>
                    <span class="text-muted">- markhazleton.com/rss.xml</span>
                <?php endif; ?>
            </small>
        </div>
    </div>
</div>

<style>
    .article-card {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .article-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }
    
    .article-card .card-title {
        font-size: 1.1rem;
        line-height: 1.3;
        margin-bottom: 0.75rem;
    }
    
    .article-card .card-text {
        font-size: 0.9rem;
        line-height: 1.4;
        color: #666;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
    }
    
    .badge {
        font-size: 0.75rem;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('articleSearch');
    const filterButtons = document.querySelectorAll('[data-filter]');
    const articles = document.querySelectorAll('.article-item');
    const articleCount = document.getElementById('articleCount');
    
    let currentFilter = 'all';
    let currentSearch = '';
    
    // Search functionality
    searchInput.addEventListener('input', function() {
        currentSearch = this.value.toLowerCase().trim();
        filterArticles();
    });
    
    // Category filter functionality
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            filterButtons.forEach(btn => btn.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            currentFilter = this.getAttribute('data-filter');
            
            // When filtering, redirect to first page with filter parameter
            const currentUrl = new URL(window.location);
            currentUrl.searchParams.set('page', 'article_list');
            currentUrl.searchParams.set('p', '1');
            if (currentFilter !== 'all') {
                currentUrl.searchParams.set('filter', currentFilter);
            } else {
                currentUrl.searchParams.delete('filter');
            }
            if (currentSearch !== '') {
                currentUrl.searchParams.set('search', currentSearch);
            } else {
                currentUrl.searchParams.delete('search');
            }
            
            window.location.href = currentUrl.toString();
        });
    });
    
    function filterArticles() {
        let visibleCount = 0;
        
        articles.forEach(article => {
            const category = article.getAttribute('data-category');
            const title = article.querySelector('.card-title').textContent.toLowerCase();
            const description = article.querySelector('.card-text').textContent.toLowerCase();
            
            const matchesFilter = currentFilter === 'all' || category === currentFilter;
            const matchesSearch = currentSearch === '' || 
                                title.includes(currentSearch) || 
                                description.includes(currentSearch) ||
                                category.toLowerCase().includes(currentSearch);
            
            if (matchesFilter && matchesSearch) {
                article.style.display = 'block';
                visibleCount++;
            } else {
                article.style.display = 'none';
            }
        });
        
        // Update article count (only for current page)
        articleCount.textContent = visibleCount;
    }
    
    // Initialize filters from URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const filterParam = urlParams.get('filter');
    const searchParam = urlParams.get('search');
    
    if (filterParam) {
        currentFilter = filterParam;
        const filterButton = document.querySelector(`[data-filter="${filterParam}"]`);
        if (filterButton) {
            filterButtons.forEach(btn => btn.classList.remove('active'));
            filterButton.classList.add('active');
        }
    }
    
    if (searchParam) {
        currentSearch = searchParam.toLowerCase().trim();
        searchInput.value = searchParam;
    }
    
    // Apply initial filters
    if (filterParam || searchParam) {
        filterArticles();
    }
});
</script>
