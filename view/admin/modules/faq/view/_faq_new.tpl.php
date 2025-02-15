<?php
   /**
    * _faq_new
    *
    * @package Wojo Framework
    * @author wojoscripts.com
    * @copyright 2023
    * @version 6.20: _faq_new.tpl.php, v1.00 5/9/2023 8:52 PM Gewa Exp $
    *
    */

   use Wojo\Language\Language;
   use Wojo\Url\Url;
   use Wojo\Utility\Utility;

   if (!defined('_WOJO')) {
      die('Direct access to this location is not allowed.');
   }
?>
<form method="post" id="wojo_form" name="wojo_form">
   <div class="wojo form">
      <div class="wojo lang tabs">
         <ul class="nav">
            <?php foreach ($this->langlist as $lang): ?>
               <li<?php echo ($lang->abbr == $this->core->lang)? ' class="active"' : null; ?>>
                  <a class="lang-color <?php echo Utility::colorToWord($lang->color); ?>"
                     data-tab="lang_<?php echo $lang->abbr; ?>"><span
                       class="flag icon <?php echo $lang->abbr; ?>"></span><?php echo $lang->name; ?></a>
               </li>
            <?php endforeach; ?>
         </ul>
         <div class="tab spaced">
            <?php foreach ($this->langlist as $lang): ?>
               <div data-tab="lang_<?php echo $lang->abbr; ?>" class="item">
                  <div class="wojo fields">
                     <div class="field">
                        <label><?php echo Language::$word->_MOD_FAQ_QUESTION; ?>
                           <small><?php echo $lang->abbr; ?></small>
                           <i class="icon asterisk"></i>
                        </label>
                        <div class="wojo large basic input">
                           <input type="text" placeholder="<?php echo Language::$word->_MOD_FAQ_QUESTION; ?>"
                                  name="question_<?php echo $lang->abbr ?>">
                        </div>
                     </div>
                  </div>
                  <div class="wojo fields">
                     <div class="field basic">
                        <textarea class="altpost" name="answer_<?php echo $lang->abbr; ?>"></textarea>
                     </div>
                  </div>
               </div>
            <?php endforeach; ?>
         </div>
      </div>
      <div class="wojo fields">
         <div class="field">
            <label><?php echo Language::$word->CATEGORY; ?>
               <i class="icon asterisk"></i>
            </label>
            <select name="category_id">
               <?php echo Utility::loopOptions($this->categories, 'id', 'name' . Language::$lang); ?>
            </select>
         </div>
         <div class="field"></div>
      </div>
   </div>
   <div class="center-align">
      <a href="<?php echo Url::url('admin/modules', 'faq/'); ?>"
         class="wojo simple small button"><?php echo Language::$word->CANCEL; ?></a>
      <button type="button" data-route="admin/modules/faq/action/" data-action="add" name="dosubmit"
              class="wojo primary button"><?php echo Language::$word->_MOD_FAQ_NEW; ?></button>
   </div>
</form>