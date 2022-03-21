<div class="container">
    <div class="row">        

        <div class="col-md-12">
            <h1>Новости</h1>
        </div>
        <div class="col-md-12">
            <ul class="nav nav-tabs">
                <?php foreach ($data['topics'] as $topic): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php if ($data['currentTopicId'] == $topic['id']): ?>active<?php endif; ?>"  href="?topic=<?= $topic['id'] ?>"><?= $topic['name'] ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="col-md-12">
            <p id="mesto" >
                <?php if ( ! empty($data['news']) ): ?>
                    <?php foreach ($data['news'] as $news): ?>
                        <div class="card col-md-12" style="margin-bottom: 20px;">
                            <div class="card-body">
                                <h5 class="card-title"><?= $news['title'] ?></h5>
                                <p class="card-text"><?= $news['text'] ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <h6>Новостей пока нет. </h6>
                <?php endif; ?>
            </p>

        </div>
    </div>
</div>
<script>
    let ws = new WebSocket('ws://127.0.0.1:61523');

    ws.addEventListener('message', (event) => {
        var div = document.createElement("div");
        if (event.data == 'ok') {
            console.info('Ok connect');
        } else {
            const msg = JSON.parse(event.data);
            div.innerHTML = '<div class="card col-md-12" style="margin-bottom: 20px;"> \
                                <div class="card-body"> \
                                    <h5 class="card-title">'+msg.title+'</h5> \
                                    <p class="card-text">'+msg.text+'</p> \
                                </div> \
                            </div>';
            document.getElementById("mesto").prepend(div);
        }
        console.info('Got message: ' + event.data);
    });

    const func = () => {
   //     ws.addEventListener('open', (event) => {
         //   console.log('connect');
            ws.send(['<?= $data['currentUser']['id']; ?>', '<?= $data['currentTopicId']; ?>']);
 //       });
    };
    setTimeout(func, 2 * 1000);


</script>