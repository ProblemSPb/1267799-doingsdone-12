<h2 class="content__main-heading">Select Project for Editing</h2>

<form class="form-edit" action="/add_project.php" method="post" autocomplete="off" enctype="multipart/form-data">

    <table>
        <tr>
            <div class="form__row-2">
                <select class="form__input form__input--select" name="project" id="project">
                    <?php foreach ($projects as $key => $value): ?>
                        <option value="<?= $value['id']; ?>"><?= $value['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <label class="checkbox-2">
                <input class="checkbox__input visually-hidden show_completed"
                       type="checkbox">
                <span class="checkbox__text">Show Tasks in the Project</span>
            </label>
        </tr>

        <tr>
            <div class="form__row-2">
                <input class="form__input <?= isset($errors['name']) ? "form__input--error" : ""; ?>" type="text" name="name"
                       id="name" value="<?= getPOSTValue('name'); ?>" placeholder="Type new title here">
                <p class="form__message"><?= $errors['name'] ?? ""; ?></p>
            </div>

            <input class="visually-hidden" type="submit" name="rename" value="rename">
            <label class="button-3 button--transparent" for="file">
                <span>Rename Project</span>
            </label>
        </tr>

    </table>


            <label class="checkbox-2">
                <input class="checkbox__input visually-hidden show_completed"
                       type="checkbox">
                <span class="checkbox__text">Purge tasks when deleting the project. If not selected, the tasks from the project will be kept under General project.</span>
            </label>

    <div class="form__row form__row--controls-2">
        <input class="button" type="submit" name="" value="Delete Project">
    </div>
</form>

<div><?= $no_tasks; ?></div>


<?php foreach ($tasks as $key => $value):?>
    <table class="tasks">
        <tr class="tasks__item task <?= (count_time_diff($value['due_date']) <= 1 && $value['due_date'] != NULL) ? 'task--important' : ''; ?>">
            <td class="task__select">
                <label class="checkbox task__checkbox">
                    <input id="<?= $value['id']; ?>" class="checkbox__input visually-hidden task__checkbox task_checker" type="checkbox"
                           value="<?= $value['id']; ?>" onclick="window.location = '/?set_task_status=<?=$value['id']?>&status=<?=$value['status']?>'" <?= ($value['status']) ? 'checked' : ''; ?>>
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
