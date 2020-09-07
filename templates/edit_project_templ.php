<h2 class="content__main-heading">Select Project for Editing</h2>

<form class="form-edit" action="/edit_project.php" method="post" autocomplete="off" enctype="multipart/form-data">

    <table>
        <tr>
            <div class="form__row-2">
                <select class="form__input form__input--select" name="project" id="project">
                    <?php foreach ($projects as $key => $value): ?>
                        <option value="<?= $value['id']; ?>"><?= $value['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </tr>

        <tr>
            <div class="form__row-2">
                <input class="form__input <?= isset($errors['rename']) ? "form__input--error" : ""; ?>" type="text"
                       name="rename"
                       id="rename" value="<?= getPOSTValue('rename'); ?>" placeholder="Type new title here">
                <p class="form__message"><?= $errors['rename'] ?? " "; ?><br></p>
            </div>

            <input class="button button--transparent" type="submit" name="" value="Rename Project">
        </tr>

    </table>

    <label class="checkbox-2">
        <input class="checkbox__input visually-hidden"
               type="checkbox" name="delete_tasks">
        <span class="checkbox__text">Purge tasks when deleting the project. If not selected, the tasks from the project will be kept under General project.</span>
    </label>

    <div class="form__row form__row--controls-2">
        <input class="button" type="submit" name="delete" value="Delete Project">
        <p class="form__message"><?= $errors['delete'] ?? " "; ?><br></p>
    </div>
</form>

<div><?= $no_tasks; ?></div>

<?php if (isset($_POST['show_tasks'])) : ?>
<?php foreach ($tasks as $key => $value): ?>
    <table class="tasks">
        <tr class="tasks__item task <?= (count_time_diff($value['due_date']) <= 1 && $value['due_date'] != NULL) ? 'task--important' : ''; ?>">
            <td class="task__select">
                <label class="checkbox task__checkbox">
                    <input id="<?= $value['id']; ?>" class="checkbox__input visually-hidden task__checkbox task_checker"
                           type="checkbox"
                           value="<?= $value['id']; ?>"
                           onclick="window.location = '/?set_task_status=<?= $value['id'] ?>&status=<?= $value['status'] ?>'" <?= ($value['status']) ? 'checked' : ''; ?>>
                    <span class="checkbox__text"> <?= $value['name']; ?> </span>
                </label>
            </td>

            <td class="task__file">
                <a class=" <?= ($value['file']) ? 'download-link' : ''; ?> "
                   href="<?= $value['file']; ?>"><?= ($value['file']) ? substr($value['file'], 9) : ''; ?></a>
            </td>
            <td class="task__date"><?= $value['due_date'] ?></td>
        </tr>
    </table>
<?php endforeach; ?>
<?php endif;?>
