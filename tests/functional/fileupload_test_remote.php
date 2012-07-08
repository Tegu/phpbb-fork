<?php
/**
 *
 * @package testing
 * @copyright (c) 2012 phpBB Group
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

/**
 * @group functional
 */
class phpbb_functional_fileupload_test_remote extends phpbb_functional_test_case
{
	protected function setUp()
	{
		// Only doing this within the functional framework because we need a
		// URL

		// Global $config required by unique_id
		// Global $user required by fileupload::remote_upload
		global $config, $user;

		if (!is_array($config))
		{
			$config = array();
		}

		$config['rand_seed'] = '';
		$config['rand_seed_last_update'] = time() + 600;

		$user = new phpbb_mock_user();
		$user->lang = new phpbb_mock_lang();
	}

	protected function tearDown()
	{
		global $config, $user;
		$user = null;
		$config = array();
	}

	public function test_invalid_extension()
	{
		$upload = new fileupload('', array('jpg'), 100);
		$file = $upload->remote_upload('http://example.com/image.gif');
		$this->assertEquals('URL_INVALID', $file->error[0]);
	}

	public function test_non_existant()
	{
		$upload = new fileupload('', array('jpg'), 100);
		$file = $upload->remote_upload('http://example.com/image.jpg');
		$this->assertEquals('EMPTY_REMOTE_DATA', $file->error[0]);
	}

	public function test_successful_upload()
	{
		$upload = new fileupload('', array('gif'), 1000);
		$file = $upload->remote_upload($this->root_url . 'styles/prosilver/theme/images/forum_read.gif');
		$this->assertEquals(0, sizeof($file->error));
		$this->assertTrue(file_exists($file->filename));
	}

	public function test_too_large()
	{
		$upload = new fileupload('', array('gif'), 100);
		$file = $upload->remote_upload($this->root_url . 'styles/prosilver/theme/images/forum_read.gif');
		$this->assertEquals('WRONG_FILESIZE', $file->error[0]);
	}
}
