<?php
?>
<h2 class="content__main-heading">Tasks List</h2>

<form class="search-form" action="index.php" method="get" autocomplete="off">
    <input class="search-form__input" type="text" name="search" value="<?= getGETValue('search'); ?>" placeholder="Find a task">

    <input class="search-form__submit" type="submit" name="" value="Искать">
</form>


<div class="tasks-controls">
    <nav class="tasks-switch">
        <a href="/<?= isset($_GET['id'])? "?id=".$_GET['id'] : "&id=0"; ?><?= isset($_GET['show_completed'])? "&show_completed=1" : ""; ?>" class="tasks-switch__item tasks-switch__item--active">All Tasks</a>
        <a href="/?filter=2<?= isset($_GET['id'])? "&id=".$_GET['id'] : "&id=0"; ?><?= isset($_GET['show_completed'])? "&show_completed=1" : ""; ?>" class="tasks-switch__item">Today</a>
        <a href="/?filter=3<?= isset($_GET['id'])? "&id=".$_GET['id'] : "&id=0"; ?><?= isset($_GET['show_completed'])? "&show_completed=1" : ""; ?>" class="tasks-switch__item">Tomorrow</a>
        <a href="/?filter=4<?= isset($_GET['id'])? "&id=".$_GET['id'] : "&id=0"; ?><?= isset($_GET['show_completed'])? "&show_completed=1" : ""; ?>" class="tasks-switch__item">Stale</a>
        <a href="/" class="tasks-switch__item">Clear Filters</a>
    </nav>

    <label class="checkbox">
        <!--добавить сюда атрибут "checked", если переменная $show_complete_tasks равна единице-->
        <input class="checkbox__input visually-hidden show_completed"
               type="checkbox" <?php if ($show_complete_tasks === 1) : ?> checked <?php endif; ?>>
        <span class="checkbox__text">Show Completed Tasks</span>
    </label>
</div>

<div><?= $no_tasks; ?></div>


<?php foreach ($tasks as $key => $value):
    if ($value['status'] && $show_complete_tasks === 0) {
        continue;
    } ?>
    <table class="tasks">
        <tr class="tasks__item task <?= ($value['status']) ? 'task--completed' : ''; ?> <?= (count_time_diff($value['due_date']) <= 1 && $value['due_date'] != NULL) ? 'task--important' : ''; ?>">
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

