<?php $user = $data['user']; ?>
<div class="container">
    <div class="row">
        <div class="col-md-12">

            <h1>Профиль</h1>

            <div class="card mb-8" style="max-width: 940px; margin-top: 100px;">
                <div class="row g-0">
                    <div class="col-md-4">
                        <svg class="bd-placeholder-img img-fluid rounded-start" width="100%" height="250" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: Avatar" preserveAspectRatio="xMidYMid slice" focusable="false"><title>Placeholder</title><rect width="100%" height="100%" fill="#868e96"/><text x="50%" y="50%" fill="#dee2e6" dy=".3em">Avatar</text></svg>
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <h5 class="card-title"><?= $user['name'] ?> </h5> <br>
                            <p class="card-text">
                                <b>Возраст:</b> <?= $user['age'] ?> <br>
                                <b>Интересы:</b> <?= $user['interests'] ?> <br>
                            </p>
                            <?php /* if ( $data['isCurrentUser']): ?>
                            <?php else: ?>
                                <p class="card-text"><small class="text-muted">Это Вы.</small></p>
                            <?php endif; */ ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>