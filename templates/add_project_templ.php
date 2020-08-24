<h2 class="content__main-heading">Adding new project</h2>

<form class="form"  action="add_project.php" method="post" autocomplete="off">
    <div class="form__row">
        <label class="form__label" for="project_name">Project name <sup>*</sup></label>
        <input class="form__input <?= isset($errors['name']) ? "form__input--error" : ""; ?>" type="text" name="name" id="project_name" value="<?= getPOSTValue('name'); ?>" placeholder="Type the name of your new project">
        <p class="form__message"><?=$errors['name'] ?? ""; ?></p>
    </div>

    <div class="form__row form__row--controls">
        <input class="button" type="submit" name="" value="Add project">
    </div>
</form>
