<div id="effectcash-form">
  <input type="hidden" name="id">
  <label><?php p($l->t('Title')); ?></label>
  <input type="text" name="title">
  <label><?php p($l->t('Group')); ?></label>
  <select name="group_title"></select>
  <label><?php p($l->t('Repeat')); ?></label>
  <select name="repeat">
    <option value=""><?php p($l->t('No repetition')); ?></option>
    <option value="w1"><?php p($l->t('Weekly')); ?></option>
    <option value="w2"><?php p($l->t('Every two weeks')); ?></option>
    <option value="m1"><?php p($l->t('Monthly')); ?></option>
    <option value="m2"><?php p($l->t('Every two months')); ?></option>
    <option value="m3"><?php p($l->t('Every three months')); ?></option>
    <option value="y1"><?php p($l->t('Yearly')); ?></option>
  </select>
  <label><?php p($l->t('Type')); ?></label>
  <select name="is_income">
    <option value="0"><?php p($l->t('Expense')); ?></option>
    <option value="1"><?php p($l->t('Revenue')); ?></option>
  </select>
  <label><?php p($l->t('Date')); ?></label>
  <input type="text" name="date" class="datepicker">
  <label><?php p($l->t('Amount')); ?></label>
  <input type="text" name="amount">
  <label><?php p($l->t('Notice')); ?></label>
  <textarea name="description"></textarea>
  <br>
  <button id="effectcash-bttn-submit" class="button"><?php p($l->t('Save')); ?></button>
  <button id="effectcash-bttn-cancel" class="button"><?php p($l->t('Cancel')); ?></button>
</div>
