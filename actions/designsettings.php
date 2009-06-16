<?php
/**
 * Laconica, the distributed open-source microblogging tool
 *
 * Change user password
 *
 * PHP version 5
 *
 * LICENCE: This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category  Settings
 * @package   Laconica
 * @author    Sarven Capadisli <csarven@controlyourself.ca>
 * @author    Zach Copley <zach@controlyourself.ca>
 * @copyright 2008-2009 Control Yourself, Inc.
 * @license   http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License version 3.0
 * @link      http://laconi.ca/
 */

if (!defined('LACONICA')) {
    exit(1);
}

require_once INSTALLDIR . '/lib/accountsettingsaction.php';
require_once INSTALLDIR . '/lib/WebColor.php';

class DesignsettingsAction extends AccountSettingsAction
{
    /**
     * Title of the page
     *
     * @return string Title of the page
     */

    function title()
    {
        return _('Profile design');
    }

    /**
     * Instructions for use
     *
     * @return instructions for use
     */

    function getInstructions()
    {
        return _('Customize the way your profile looks with a background image and a colour palette of your choice.');
    }

    /**
     * Content area of the page
     *
     * Shows a form for changing the password
     *
     * @return void
     */

    function showContent()
    {
        $user = common_current_user();
        $this->elementStart('form', array('method' => 'post',
                                          'id' => 'form_settings_design',
                                          'class' => 'form_settings',
                                          'action' =>
                                          common_local_url('designsettings')));
        $this->elementStart('fieldset');
        $this->hidden('token', common_session_token());

        $this->elementStart('fieldset', array('id' => 'settings_design_background-image'));
        $this->element('legend', null, _('Change background image'));
        $this->elementStart('ul', 'form_data');
        $this->elementStart('li');
        $this->element('label', array('for' => 'design_background-image_file'),
                                _('Upload file'));
        $this->element('input', array('name' => 'design_background-image_file',
                                      'type' => 'file',
                                      'id' => 'design_background-image_file'));
        $this->element('p', 'form_guide', _('You can upload your personal background image. The maximum file size is 2Mb.'));
        $this->element('input', array('name' => 'MAX_FILE_SIZE',
                                      'type' => 'hidden',
                                      'id' => 'MAX_FILE_SIZE',
                                      'value' => ImageFile::maxFileSizeInt()));
        $this->elementEnd('li');
        $this->elementEnd('ul');
        $this->elementEnd('fieldset');

        $this->elementStart('fieldset', array('id' => 'settings_design_color'));
        $this->element('legend', null, _('Change colours'));
        $this->elementStart('ul', 'form_data');

        $design = $user->getDesign();

        if (empty($design)) {
            $design = $this->defaultDesign();
        }

        try {

            $bgcolor = new WebColor($design->backgroundcolor);

            $this->elementStart('li');
            $this->element('label', array('for' => 'swatch-0'), _('Background'));
            $this->element('input', array('name' => 'design_background',
                                          'type' => 'text',
                                          'id' => 'swatch-0',
                                          'class' => 'swatch',
                                          'maxlength' => '7',
                                          'size' => '7',
                                          'value' => '#' . $bgcolor->hexValue()));
            $this->elementEnd('li');


            $ccolor = new WebColor($design->contentcolor);

            $this->elementStart('li');
            $this->element('label', array('for' => 'swatch-1'), _('Content'));
            $this->element('input', array('name' => 'design_content',
                                          'type' => 'text',
                                          'id' => 'swatch-1',
                                          'class' => 'swatch',
                                          'maxlength' => '7',
                                          'size' => '7',
                                          'value' => '#' . $ccolor->hexValue()));
            $this->elementEnd('li');

            $sbcolor = new WebColor($design->sidebarcolor);

            $this->elementStart('li');
            $this->element('label', array('for' => 'swatch-2'), _('Sidebar'));
            $this->element('input', array('name' => 'design_sidebar',
                                        'type' => 'text',
                                        'id' => 'swatch-2',
                                        'class' => 'swatch',
                                        'maxlength' => '7',
                                        'size' => '7',
                                        'value' => '#' . $sbcolor->hexValue()));
            $this->elementEnd('li');

            $tcolor = new WebColor($design->textcolor);

            $this->elementStart('li');
            $this->element('label', array('for' => 'swatch-3'), _('Text'));
            $this->element('input', array('name' => 'design_text',
                                        'type' => 'text',
                                        'id' => 'swatch-3',
                                        'class' => 'swatch',
                                        'maxlength' => '7',
                                        'size' => '7',
                                        'value' => '#' . $tcolor->hexValue()));
            $this->elementEnd('li');

            $lcolor = new WebColor($design->linkcolor);

            $this->elementStart('li');
            $this->element('label', array('for' => 'swatch-4'), _('Links'));
            $this->element('input', array('name' => 'design_links',
                                         'type' => 'text',
                                         'id' => 'swatch-4',
                                         'class' => 'swatch',
                                         'maxlength' => '7',
                                         'size' => '7',
                                         'value' => '#' . $lcolor->hexValue()));

           $this->elementEnd('li');

       } catch (WebColorException $e) {
           common_log(LOG_ERR, 'Bad color values in design ID: ' .
               $design->id);
       }

       $this->elementEnd('ul');
       $this->elementEnd('fieldset');

       $this->element('input', array('id' => 'settings_design_reset',
                                     'type' => 'reset',
                                     'value' => 'Reset',
                                     'class' => 'submit form_action-primary',
                                     'title' => _('Reset back to default')));

        $this->submit('save', _('Save'), 'submit form_action-secondary',
            'save', _('Save design'));

        $this->elementEnd('fieldset');
        $this->elementEnd('form');
    }

    /**
     * Handle a post
     *
     * Validate input and save changes. Reload the form with a success
     * or error message.
     *
     * @return void
     */

    function handlePost()
    {
        // CSRF protection
        $token = $this->trimmed('token');
        if (!$token || $token != common_session_token()) {
            $this->showForm(_('There was a problem with your session token. '.
                              'Try again, please.'));
            return;
        }

        if ($this->arg('save')) {
            $this->saveDesign();
        } else if ($this->arg('reset')) {
            $this->resetDesign();
        } else {
            $this->showForm(_('Unexpected form submission.'));
        }
    }

    /**
     * Add the Farbtastic stylesheet
     *
     * @return void
     */

    function showStylesheets()
    {
        parent::showStylesheets();
        $farbtasticStyle =
          common_path('theme/base/css/farbtastic.css?version='.LACONICA_VERSION);

        $this->element('link', array('rel' => 'stylesheet',
                                     'type' => 'text/css',
                                     'href' => $farbtasticStyle,
                                     'media' => 'screen, projection, tv'));
    }

    /**
     * Add the Farbtastic scripts
     *
     * @return void
     */

    function showScripts()
    {
        parent::showScripts();

        $farbtasticPack = common_path('js/farbtastic/farbtastic.js');
        $farbtasticGo   = common_path('js/farbtastic/farbtastic.go.js');

        $this->element('script', array('type' => 'text/javascript',
                                       'src' => $farbtasticPack));
        $this->element('script', array('type' => 'text/javascript',
                                       'src' => $farbtasticGo));
    }

    /**
     * Get a default user design
     *
     * @return Design design
     */

    function defaultDesign()
    {
        $defaults = common_config('site', 'design');

        $design = new Design();

        try {

            $color = new WebColor();

            $color->parseColor($defaults['backgroundcolor']);
            $design->backgroundcolor = $color->intValue();

            $color->parseColor($defaults['contentcolor']);
            $design->contentcolor = $color->intValue();

            $color->parseColor($defaults['sidebarcolor']);
            $design->sidebarcolor = $color->intValue();

            $color->parseColor($defaults['textcolor']);
            $design->sidebarcolor = $color->intValue();

            $color->parseColor($defaults['linkcolor']);
            $design->linkcolor = $color->intValue();

            $design->backgroundimage = $defaults['backgroundimage'];

        } catch (WebColorException $e) {
            common_log(LOG_ERR, _('Bad default color settings: ' .
                $e->getMessage()));
        }

        return $design;
    }

    /**
     * Save or update the user's design settings
     *
     * @return void
     */

    function saveDesign()
    {
        $user = common_current_user();

        try {

            $bgcolor = new WebColor($this->trimmed('design_background'));
            $ccolor  = new WebColor($this->trimmed('design_content'));
            $sbcolor = new WebColor($this->trimmed('design_sidebar'));
            $tcolor  = new WebColor($this->trimmed('design_text'));
            $lcolor  = new WebColor($this->trimmed('design_links'));

        } catch (WebColorException $e) {
            $this->showForm($e->getMessage());
            return;
        }

        $design = User::getDesign();

        if (!isset($design)) {

            $original = clone($design);

            $original->backgroundcolor = $bgcolor->intValue();
            $original->contentcolor    = $ccolor->intValue();
            $original->sidebarcolor    = $sbcolor->intValue();
            $original->textcolor       = $tcolor->intValue();
            $original->linkcolor       = $lcolor->intValue();

            $result = $design->update($original);

            if ($result === false) {
                common_log_db_error($design, 'UPDATE', __FILE__);
                $this->showForm(_('Couldn\'t update your design.'));
                return;
            }

            // update design
        } else {

            $user->query('BEGIN');

            // save new design
            $design = new Design();

            $design->backgroundcolor = $bgcolor->intValue();
            $design->contentcolor    = $ccolor->intValue();
            $design->sidebarcolor    = $sbcolor->intValue();
            $design->textcolor       = $tcolor->intValue();
            $design->linkcolor       = $lcolor->intValue();
            $design->backgroundimage = $defaults['backgroundimage'];

            $id = $design->insert();

            if (empty($id)) {
                common_log_db_error($id, 'INSERT', __FILE__);
                $this->showForm(_('Unable to save your design settings!'));
                return;
            }

            $original = clone($user);
            $user->design_id = $id;
            $result = $user->update($original);

            if (empty($result)) {
                common_log_db_error($original, 'UPDATE', __FILE__);
                $this->showForm(_('Unable to save your design settings!'));
                $user->query('ROLLBACK');
                return;
            }

            $user->query('COMMIT');

        }

        $this->showForm(_('Design preferences saved.'), true);
    }

    /**
     * Reset design settings to previous saved value if any, or
     * the defaults
     *
     * @return void
     */

    function resetDesign()
    {
        $this->showForm(_('Design preferences reset.'), true);
    }

}
