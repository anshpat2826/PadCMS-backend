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

class PageCopyToRevisionTest extends AM_Test_PHPUnit_DatabaseTestCase
{
    protected function _getDataSetYmlFile()
    {
        return dirname(__FILE__)
                . DIRECTORY_SEPARATOR . '_fixtures'
                . DIRECTORY_SEPARATOR . 'PageCopyToRevisionTest.yml';
    }

    public function setUp()
    {
        parent::setUp();

        $this->page = AM_Model_Db_Table_Abstract::factory('page')->findOneBy(array('id' => 1));

        $this->elementsMock       = $this->getMock('AM_Model_Db_Rowset_Element', array('copyToPage'), array(array("readOnly" => true)));
        $this->pageBackgroundMock = $this->getMock('AM_Model_Db_PageBackground', array('copyToPage'), array(array("readOnly" => true, "table" => new AM_Model_Db_Table_PageBackground())));
        $this->termMock           = $this->getMock('AM_Model_Db_Term', array('saveToPage'), array(array("readOnly" => true, "table" => new AM_Model_Db_Table_PageBackground())));

        $this->page->setElements($this->elementsMock);
        $this->page->setPageBackground($this->pageBackgroundMock);
        $this->page->addTerm($this->termMock);
    }

    public function testShouldCopyToRevision()
    {
        //GIVEN
        $revisionData = array("id" => 2, "title" => "test_revision2", "state" => 1, "issue" => 2, "user" => 2);
        $revisionTo = new AM_Model_Db_Revision();
        $revisionTo->setFromArray($revisionData);
        $revisionTo->save();

        //THEN
        $this->elementsMock->expects($this->once())
            ->method('copyToPage');
        $this->pageBackgroundMock->expects($this->once())
            ->method('copyToPage');
        $this->termMock->expects($this->once())
            ->method('saveToPage');

        //WHEN
        $this->page->copyToRevision($revisionTo);

        //THEN
        $this->assertEquals(2, $this->page->user, "User id should change");
        $this->assertEquals(2, $this->page->revision, "Revision id should change");

        $queryTable    = $this->getConnection()->createQueryTable("page", "SELECT id, user, revision FROM page ORDER BY id");
        $expectedTable = $this->createFlatXMLDataSet(dirname(__FILE__) . "/_dataset/copy2revision.xml")
                              ->getTable("page");

        $this->assertTablesEqual($expectedTable, $queryTable);
    }
}
