<?php

/**
 * Testing the controller functionality.
 *
 * @category  Testing
 * @package   XH
 * @author    The CMSimple_XH developers <devs@cmsimple-xh.org>
 * @copyright 2014-2017 The CMSimple_XH developers <http://cmsimple-xh.org/?The_Team>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link      http://cmsimple-xh.org/
 */

namespace XH;

use org\bovigo\vfs\vfsStreamWrapper;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStream;

/**
 * Testing the handling of file view requests.
 *
 * @category Testing
 * @package  XH
 * @author   The CMSimple_XH developers <devs@cmsimple-xh.org>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://cmsimple-xh.org/
 * @since    1.6.3
 */
class ControllerFileViewTest extends TestCase
{
    /**
     * The test subject.
     *
     * @var Controller
     */
    protected $subject;

    /**
     * The XH_exit() mock.
     *
     * @var object
     */
    protected $exitMock;

    /**
     * The header() mock.
     *
     * @var object
     */
    protected $headerMock;

    /**
     * The XH_logFileView() mock.
     *
     * @var object
     */
    protected $logFileViewMock;

    /**
     * Sets up the test fixture.
     *
     * @return void
     *
     * @global string The name of a special file to be handled.
     */
    public function setUp()
    {
        global $file;

        $this->setUpFileSystem();
        $file = 'content';
        $this->subject = new Controller();
        $this->exitMock = $this->getFunctionMock('XH_exit', $this->subject);
        $this->headerMock = $this->getFunctionMock('header', $this->subject);
        $this->logFileViewMock = $this->getFunctionMock('XH_logFileView', $this->subject);
    }

    /**
     * Sets up the file system.
     *
     * @return void
     *
     * @global array The paths of system files and folders.
     */
    protected function setUpFileSystem()
    {
        global $pth;

        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('test'));
        $pth['file']['content'] = vfsStream::url('test/content.htm');
        file_put_contents($pth['file']['content'], 'foo');
    }

    /**
     * Tests that the Content-Type header is sent.
     *
     * @return void
     */
    public function testSendsContentTypeHeader()
    {
        $this->headerMock->expects($this->once())
            ->with('Content-Type: text/plain; charset=utf-8');
        $this->handleFileView();
    }

    /**
     * Tests that the file contents are output.
     *
     * @return void
     */
    public function testOutputsFileContents()
    {
        $this->expectOutputString('foo');
        $this->subject->handleFileView();
    }

    /**
     * Tests that the script is exited.
     *
     * @return void
     */
    public function testExitsScript()
    {
        $this->exitMock->expects($this->once());
        $this->handleFileView();
    }

    /**
     * Calls Controller::handleFileView() while buffering output.
     *
     * @return void
     */
    protected function handleFileView()
    {
        ob_start();
        $this->subject->handleFileView();
        ob_end_clean();
    }

    /**
     * Tests the log file view.
     *
     * @return void
     *
     * @global string The name of a special file to be handled.
     */
    public function testLogFile()
    {
        global $file;

        $file = 'log';
        $this->logFileViewMock->expects($this->once());
        $this->subject->handleFileView();
    }
}