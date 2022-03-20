<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Добавить новость</h1>
                <form action="#" method="post">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Заголовок</label>
                                <input type="text" class="form-control" id="title" name="title" placeholder="Заголовок" value="">
                            </div>
                            <div class="form-group">
                                <label for="text">Текст</label>
                                <textarea name="text" class="form-control" id="text" cols="30" rows="5"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="text">Выберите категории</label>
                                <select name="topic" id="topic" class="form-control">
                                <?php if ( ! empty($data['topics']) ): ?>
                                    <?php foreach ($data['topics'] as $topic): ?>
                                        <option value="<?= $topic['id'] ?>"><?= $topic['name'] ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                        </div>
                    <button type="submit" class="btn btn-primary">Добавить</button>
                    </div>
                </form>
        </div>
    </div>
</div>