<h2 class="content__main-heading">Adding new task</h2>

<form class="form"  action="/add.php" method="post" autocomplete="off" enctype="multipart/form-data">
  <div class="form__row">
    <label class="form__label" for="name">Title <sup>*</sup></label>

    <input class="form__input <?= isset($errors['name']) ? "form__input--error" : ""; ?>" type="text" name="name" id="name" value="<?= getPOSTValue('name'); ?>" placeholder="Type the title here">
    <p class="form__message"><?=$errors['name'] ?? ""; ?></p>
  </div>

  <div class="form__row">
    <label class="form__label" for="project">Project <sup>*</sup></label>

    <select class="form__input form__input--select" name="project" id="project">
    <?php foreach ($projects as $key => $value): ?>
      <option value="<?= $value['id']; ?>"><?= $value['name']; ?></option>
    <?php endforeach; ?>
    </select>
  </div>

  <div class="form__row">
    <label class="form__label" for="date">Due date</label>

    <input class="form__input form__input--date <?= isset($errors['date']) ? "form__input--error" : ""; ?>" type="text" name="date" id="date" value="<?= getPOSTValue('date'); ?>" placeholder="Due date YYYY-MM-DD">
    <p class="form__message"><?=$errors['date'] ?? ""; ?></p>
  </div>

  <div class="form__row">
    <label class="form__label" for="file">Attach file</label>

    <div class="form__input-file">
      <input class="visually-hidden" type="file" name="file" id="file" value="">

      <label class="button button--transparent" for="file">
        <span>Select a file</span>
      </label>
      <p class="form__message"><?=$errors['file'] ?? ""; ?></p>
    </div>
  </div>

  <div class="form__row form__row--controls">
    <input class="button" type="submit" name="" value="Add">
  </div>
</form>
