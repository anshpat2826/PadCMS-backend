<?php
/**
 * LICENSE
 *
 * This software is governed by the CeCILL-C  license under French law and
 * abiding by the rules of distribution of free software.  You can  use,
 * modify and/ or redistribute the software under the terms of the CeCILL-C
 * license as circulated by CEA, CNRS and INRIA at the following URL
 * "http://www.cecill.info".
 *
 * As a counterpart to the access to the source code and  rights to copy,
 * modify and redistribute granted by the license, users are provided only
 * with a limited warranty  and the software's author,  the holder of the
 * economic rights,  and the successive licensors  have only  limited
 * liability.
 *
 * In this respect, the user's attention is drawn to the risks associated
 * with loading,  using,  modifying and/or developing or reproducing the
 * software by the user in light of its specific status of free software,
 * that may mean  that it is complicated to manipulate,  and  that  also
 * therefore means  that it is reserved for developers  and  experienced
 * professionals having in-depth computer knowledge. Users are therefore
 * encouraged to load and test the software's suitability as regards their
 * requirements in conditions enabling the security of their systems and/or
 * data to be ensured and,  more generally, to use and operate it in the
 * same conditions as regards security.
 *
 * The fact that you are presently reading this means that you have had
 * knowledge of the CeCILL-C license and that you accept its terms.
 *
 * @author Copyright (c) PadCMS (http://www.padcms.net)
 * @version $DOXY_VERSION
 */

class ApplicationCopyTest extends AM_Test_PHPUnit_DatabaseTestCase
{
    /** @var AM_Model_Db_Application */
    protected $_oApplication = null;
    /** @var AM_Model_Db_User */
    protected $_oUser = null;
    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_oIssueSetMock = null;

    protected function _getDataSetYmlFile()
    {
        return dirname(__FILE__)
                . DIRECTORY_SEPARATOR . '_fixtures'
                . DIRECTORY_SEPARATOR . 'ApplicationCopyTest.yml';
    }

    public function setUp()
    {
        parent::setUp();

        $this->_oApplication  = AM_Model_Db_Table_Abstract::factory('application')->findOneBy(array('id' => 1));
        $this->_oUser         = AM_Model_Db_Table_Abstract::factory('user')->findOneBy(array('id' => 2));
        $this->_oIssueSetMock = $this->getMock('AM_Model_Db_Rowset_Issue', array('copyToUser'), array(array("readOnly" => true)));
        $this->_oApplication->setIssues($this->_oIssueSetMock);
    }

    public function testShouldCopyToUser()
    {
        //THEN
        $this->_oIssueSetMock->expects($this->once())
            ->method('copyToUser')
            ->with($this->equalTo($this->_oUser));

        //WHEN
        $this->_oApplication->copyToUser($this->_oUser);
        $this->_oApplication->refresh();

        //THEN
        $this->assertEquals(2, $this->_oApplication->client, 'Client id should change');

        $oGivenDataSet    = $this->getConnection()->createQueryTable('application', 'SELECT id, client FROM application ORDER BY id');
        $oExpectedDataSet = $this->createFlatXMLDataSet(dirname(__FILE__) . '/_dataset/copy.xml')
                              ->getTable('application');

        $this->assertTablesEqual($oExpectedDataSet, $oGivenDataSet);
    }
}
