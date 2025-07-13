<?php
require_once 'database.php';

// 获取推荐内容
$db = Database::getInstance();
$learningResources = $db->getRecommendationsByCategory('学习资源');
$toolRecommendations = $db->getRecommendationsByCategory('工具推荐');
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>推荐阅读</title>
    <link rel="icon" href="./static/img/icon/icon-nav.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" href="./static/css/style.css">
    <link rel="stylesheet" href="./static/css/root.css">
    <link rel="stylesheet" href="./static/css/blog.css">
</head>

<body>
    <div id="loading">
        <div id="loading-center"></div>
    </div>
    <div class="filter"></div>

    <!-- 顶部导航栏 -->
    <nav class="top-nav">
        <div class="nav-container">
            <div class="nav-logo">
                <a href="index.php">
                    <img src="./static/img/icon/icon-nav.png" alt="Logo">
                    <span>Yizhe Xie</span>
                </a>
            </div>
            <div class="nav-links">
                <a href="index.php">首页</a>
                <a href="blog.php">博客</a>
                <a href="articles.php" class="active">推荐阅读</a>
                <a href="about.php">关于我</a>
            </div>
            <div class="nav-admin">
                <a href="admin/login.php" class="admin-login-btn">
<svg t="1752382187165" class="icon" viewBox="0 0 1164 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="2806" width="200" height="200"><path d="M1047.326501 0 116.366581 0c-64.006392 0-116.366581 52.367007-116.366581 116.366581l0 640.029831c0 64.006392 51.344214 126.642242 114.116436 139.195323l254.375464 50.873729c0 0-218.939092 77.575448-77.568629 77.575448l581.846541 0c141.390919 0-77.568629-77.575448-77.568629-77.575448l254.395919-50.873729c62.751766-12.553081 114.09598-75.18893 114.09598-139.195323L1163.693082 116.366581C1163.693082 52.367007 1111.326075 0 1047.326501 0zM1047.326501 750.580129 116.366581 750.580129 116.366581 104.734014l930.953102 0L1047.319683 750.580129z" fill="#040000" p-id="2807"></path></svg>
                    后台管理
                </a>
            </div>
            <div class="nav-toggle">
                <div class="toggle-line"></div>
                <div class="toggle-line"></div>
                <div class="toggle-line"></div>
            </div>
        </div>
    </nav>

    <div class="main">
        <div class="left">
            <div class="logo">
            </div>
            <div class="left-div left-des">
                <div class="left-des-item">
                    <svg t="1705773709627" class="icon" viewBox="0 0 1024 1024" version="1.1"
                        xmlns="http://www.w3.org/2000/svg" p-id="1478">
                        <path
                            d="M512 249.976471c-99.388235 0-180.705882 81.317647-180.705882 180.705882s81.317647 180.705882 180.705882 180.705882 180.705882-81.317647 180.705882-180.705882-81.317647-180.705882-180.705882-180.705882z m0 301.17647c-66.258824 0-120.470588-54.211765-120.470588-120.470588s54.211765-120.470588 120.470588-120.470588 120.470588 54.211765 120.470588 120.470588-54.211765 120.470588-120.470588 120.470588z"
                            p-id="1479"></path>
                        <path
                            d="M512 39.152941c-216.847059 0-391.529412 174.682353-391.529412 391.529412 0 349.364706 391.529412 572.235294 391.529412 572.235294s391.529412-222.870588 391.529412-572.235294c0-216.847059-174.682353-391.529412-391.529412-391.529412z m0 891.482353C424.658824 873.411765 180.705882 686.682353 180.705882 430.682353c0-183.717647 147.576471-331.294118 331.294118-331.294118s331.294118 147.576471 331.294118 331.294118c0 256-243.952941 442.729412-331.294118 499.952941z"
                            p-id="1480"></path>
                    </svg>
                    China-Hainan
                </div>
                <div class="left-des-item">
                    <svg t="1705773906032" class="icon" viewBox="0 0 1024 1024" version="1.1"
                        xmlns="http://www.w3.org/2000/svg" p-id="2474">
                        <path
                            d="M729.6 234.666667H294.4V157.866667a51.2 51.2 0 0 1 51.2-51.2h332.8a51.2 51.2 0 0 1 51.2 51.2v76.8z m179.2 51.2a51.2 51.2 0 0 1 51.2 51.2v512a51.2 51.2 0 0 1-51.2 51.2H115.2a51.2 51.2 0 0 1-51.2-51.2v-512a51.2 51.2 0 0 1 51.2-51.2h793.557333z m-768 172.032c0 16.384 13.312 29.696 29.696 29.696h683.008a29.696 29.696 0 1 0 0-59.392H170.410667a29.696 29.696 0 0 0-29.696 29.696z m252.416 118.784c0 16.384 13.312 29.696 29.696 29.696h178.176a29.696 29.696 0 1 0 0-59.392H422.912a29.738667 29.738667 0 0 0-29.696 29.696z"
                            p-id="2475"></path>
                    </svg>
                    MUC & CityU MAC
                </div>
            </div>
            <!-- 随机名言区域 -->
            <div class="quote-box">
                <div class="quote-content"></div>
                <div class="quote-refresh">
                    <button>
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17.65 6.35A7.958 7.958 0 0 0 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08A5.99 5.99 0 0 1 12 18c-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div class="right">
            <header>
                <div class="index-logo">
                </div>
                <div class="welcome">
                    推荐阅读
                </div>
                <div class="description">✨ 精选资源与好文 <span class="purpleText">Reading & Learning</span>
                </div>

                <div class="iconContainer">
                    <a class="iconItem" href="index.php">
                        <svg t="1742999202713" class="icon" viewBox="0 0 1029 1024" version="1.1"
                            xmlns="http://www.w3.org/2000/svg" p-id="1490" width="200" height="200">
                            <path
                                d="M1001.423238 494.592q21.504 20.48 22.528 45.056t-16.384 40.96q-19.456 17.408-45.056 16.384t-40.96-14.336q-5.12-4.096-31.232-28.672t-62.464-58.88-77.824-73.728-78.336-74.24-63.488-60.416-33.792-31.744q-32.768-29.696-64.512-28.672t-62.464 28.672q-10.24 9.216-38.4 35.328t-65.024 60.928-77.824 72.704-75.776 70.656-59.904 55.808-30.208 27.136q-15.36 12.288-40.96 13.312t-44.032-15.36q-20.48-18.432-19.456-44.544t17.408-41.472q6.144-6.144 37.888-35.84t75.776-70.656 94.72-88.064 94.208-88.064 74.752-70.144 36.352-34.304q38.912-37.888 83.968-38.4t76.8 30.208q6.144 5.12 25.6 24.064t47.616 46.08 62.976 60.928 70.656 68.096 70.144 68.096 62.976 60.928 48.128 46.592zM447.439238 346.112q25.6-23.552 61.44-25.088t64.512 25.088q3.072 3.072 18.432 17.408l38.912 35.84q22.528 21.504 50.688 48.128t57.856 53.248q68.608 63.488 153.6 142.336l0 194.56q0 22.528-16.896 39.936t-45.568 18.432l-193.536 0 0-158.72q0-33.792-31.744-33.792l-195.584 0q-17.408 0-24.064 10.24t-6.656 23.552q0 6.144-0.512 31.232t-0.512 53.76l0 73.728-187.392 0q-29.696 0-47.104-13.312t-17.408-37.888l0-203.776q83.968-76.8 152.576-139.264 28.672-26.624 57.344-52.736t52.224-47.616 39.424-36.352 19.968-18.944z"
                                p-id="1491"></path>
                        </svg>
                        <div class="iconTip">Home</div>
                    </a><a class="iconItem" href="#">
                        <svg t="1704870335945" class="icon" viewBox="0 0 1024 1024" version="1.1"
                            xmlns="http://www.w3.org/2000/svg" p-id="2487">
                            <path
                                d="M511.6 76.3C264.3 76.2 64 276.4 64 523.5 64 718.9 189.3 885 363.8 946c23.5 5.9 19.9-10.8 19.9-22.2v-77.5c-135.7 15.9-141.2-73.9-150.3-88.9C215 726 171.5 718 184.5 703c30.9-15.9 62.4 4 98.9 57.9 26.4 39.1 77.9 32.5 104 26 5.7-23.5 17.9-44.5 34.7-60.8-140.6-25.2-199.2-111-199.2-213 0-49.5 16.3-95 48.3-131.7-20.4-60.5 1.9-112.3 4.9-120 58.1-5.2 118.5 41.6 123.2 45.3 33-8.9 70.7-13.6 112.9-13.6 42.4 0 80.2 4.9 113.5 13.9 11.3-8.6 67.3-48.8 121.3-43.9 2.9 7.7 24.7 58.3 5.5 118 32.4 36.8 48.9 82.7 48.9 132.3 0 102.2-59 188.1-200 212.9 23.5 23.2 38.1 55.4 38.1 91v112.5c0.8 9 0 17.9 15 17.9 177.1-59.7 304.6-227 304.6-424.1 0-247.2-200.4-447.3-447.5-447.3z"
                                p-id="2488"></path>
                        </svg>
                        <div class="iconTip">Github</div>
                    </a><a class="iconItem" href="mailto:xieyizhe66@gmail.com">
                        <svg t="1704870588438" class="icon" viewBox="0 0 1024 1024" version="1.1"
                            xmlns="http://www.w3.org/2000/svg" p-id="3174">
                            <path
                                d="M926.47619 355.644952V780.190476a73.142857 73.142857 0 0 1-73.142857 73.142857H170.666667a73.142857 73.142857 0 0 1-73.142857-73.142857V355.644952l304.103619 257.828572a170.666667 170.666667 0 0 0 220.745142 0L926.47619 355.644952zM853.333333 170.666667a74.044952 74.044952 0 0 1 26.087619 4.778666 72.704 72.704 0 0 1 30.622477 22.186667 73.508571 73.508571 0 0 1 10.678857 17.67619c3.169524 7.509333 5.12 15.652571 5.607619 24.210286L926.47619 243.809524v24.380952L559.469714 581.241905a73.142857 73.142857 0 0 1-91.306666 2.901333l-3.632762-2.925714L97.52381 268.190476v-24.380952a72.899048 72.899048 0 0 1 40.155428-65.292191A72.97219 72.97219 0 0 1 170.666667 170.666667h682.666666z"
                                p-id="3175"></path>
                        </svg>
                        <div class="iconTip">Mail</div>
                    </a><a class="switch" href="javascript:void(0)">
                        <div class="onoffswitch">
                            <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch"
                                checked>
                            <label class="onoffswitch-label" for="myonoffswitch">
                                <span class="onoffswitch-inner"></span>
                                <span class="onoffswitch-switch"></span>
                            </label>
                        </div>
                    </a>
                </div>
            </header>

            <main>
                <!-- 学习资源区块 -->
                <div class="title">
                    <svg t="1705257823317" class="icon" viewBox="0 0 1024 1024" version="1.1"
                        xmlns="http://www.w3.org/2000/svg" p-id="7833">
                        <path
                            d="M395.765333 586.570667h-171.733333c-22.421333 0-37.888-22.442667-29.909333-43.381334L364.768 95.274667A32 32 0 0 1 394.666667 74.666667h287.957333c22.72 0 38.208 23.018667 29.632 44.064l-99.36 243.882666h187.050667c27.509333 0 42.186667 32.426667 24.042666 53.098667l-458.602666 522.56c-22.293333 25.408-63.626667 3.392-54.976-29.28l85.354666-322.421333zM416.714667 138.666667L270.453333 522.581333h166.869334a32 32 0 0 1 30.933333 40.181334l-61.130667 230.954666 322.176-367.114666H565.312c-22.72 0-38.208-23.018667-29.632-44.064l99.36-243.882667H416.714667z"
                            p-id="7834"></path>
                    </svg>
                    学习资源
                </div>

                <div class="projectList">
                    <?php if (!empty($learningResources)): ?>
                        <?php foreach ($learningResources as $resource): ?>
                            <a class="recommendCard" href="<?php echo htmlspecialchars($resource['url']); ?>">
                                <div class="recommendCard-img">
                                    <img src="<?php echo htmlspecialchars($resource['image'] ?: './static/img/background/background1.png'); ?>" 
                                         alt="<?php echo htmlspecialchars($resource['title']); ?>">
                                    <div class="recommendCard-overlay">
                                        <div class="recommendCard-overlay-title"><?php echo htmlspecialchars($resource['title']); ?></div>
                                    </div>
                                </div>
                                <div class="recommendCard-content">
                                    <div class="recommendCard-meta">
                                        <div>
                                            <?php if (!empty($resource['tags'])): ?>
                                                <?php foreach (explode(',', $resource['tags']) as $tag): ?>
                                                    <span class="recommendCard-tag"># <?php echo htmlspecialchars(trim($tag)); ?></span>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                        <div class="recommendCard-date"><?php echo date('Y-m-d', strtotime($resource['date'])); ?></div>
                                    </div>
                                    <div class="recommendCard-desc"><?php echo htmlspecialchars($resource['description']); ?></div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-content">
                            <p>暂无学习资源推荐</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- 工具推荐区块 -->
                <div class="title title-margin-top">
                    <svg t="1705257823317" class="icon" viewBox="0 0 1024 1024" version="1.1"
                        xmlns="http://www.w3.org/2000/svg" p-id="7833">
                        <path
                            d="M395.765333 586.570667h-171.733333c-22.421333 0-37.888-22.442667-29.909333-43.381334L364.768 95.274667A32 32 0 0 1 394.666667 74.666667h287.957333c22.72 0 38.208 23.018667 29.632 44.064l-99.36 243.882666h187.050667c27.509333 0 42.186667 32.426667 24.042666 53.098667l-458.602666 522.56c-22.293333 25.408-63.626667 3.392-54.976-29.28l85.354666-322.421333zM416.714667 138.666667L270.453333 522.581333h166.869334a32 32 0 0 1 30.933333 40.181334l-61.130667 230.954666 322.176-367.114666H565.312c-22.72 0-38.208-23.018667-29.632-44.064l99.36-243.882667H416.714667z"
                            p-id="7834"></path>
                    </svg>
                    工具推荐
                </div>

                <div class="projectList">
                    <?php if (!empty($toolRecommendations)): ?>
                        <?php foreach ($toolRecommendations as $tool): ?>
                            <a class="recommendCard" href="<?php echo htmlspecialchars($tool['url']); ?>">
                                <div class="recommendCard-img">
                                    <img src="<?php echo htmlspecialchars($tool['image'] ?: './static/img/background/background2.png'); ?>" 
                                         alt="<?php echo htmlspecialchars($tool['title']); ?>">
                                    <div class="recommendCard-overlay">
                                        <div class="recommendCard-overlay-title"><?php echo htmlspecialchars($tool['title']); ?></div>
                                    </div>
                                </div>
                                <div class="recommendCard-content">
                                    <div class="recommendCard-meta">
                                        <div>
                                            <?php if (!empty($tool['tags'])): ?>
                                                <?php foreach (explode(',', $tool['tags']) as $tag): ?>
                                                    <span class="recommendCard-tag"># <?php echo htmlspecialchars(trim($tag)); ?></span>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                        <div class="recommendCard-date"><?php echo date('Y-m-d', strtotime($tool['date'])); ?></div>
                                    </div>
                                    <div class="recommendCard-desc"><?php echo htmlspecialchars($tool['description']); ?></div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-content">
                            <p>暂无工具推荐</p>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <!-- 尾注 -->
    <footer>
        Yizhe Xie &copy; 2025 | ID: 23160151
    </footer>

    <div class="tc">
        <div class="tc-main">
            <img class="tc-img" src="" alt="" srcset="">
        </div>
    </div>

    <script src="./static/js/script.js"></script>
</body>

</html> 