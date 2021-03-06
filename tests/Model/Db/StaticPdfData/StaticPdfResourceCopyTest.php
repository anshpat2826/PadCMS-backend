<?php
/**
 * LICENSE
 *
 * This software is governed by the CeCILL-C  license under French law and
 * abiding by the rules of distribution of free software.  You can  use,
 * modify and/ or redistribute the software under the terms of the CeCILL-C
 * license as circulated by CEA, CNRS and INRIA at the following URL
 * 'http://www.cecill.info'.
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

class StaticPdfResourceCopyTest extends PHPUnit_Framework_TestCase
{
    /** @var PHPUnit_Framework_MockObject_MockObject $standardMock **/
    protected $_oStandardMock = null;
    protected $_oStaticPdf = null;

    public function setUp()
    {
        $this->_oStaticPdf = new AM_Model_Db_StaticPdf();
        $this->_oStaticPdf->setFromArray(array('id' => 1, 'issue' => 1, 'name' => 'resource.pdfmock'));

        $this->_oStandardMock = $this->getMock('AM_Tools_Standard', array('is_dir', 'mkdir', 'copy'));
    }

    public function testShouldCopyResource()
    {
        //GIVEN
        $oResource = new AM_Model_Db_StaticPdf_Data_Resource($this->_oStaticPdf);
        $this->_oStaticPdf->id    = 2;
        $this->_oStaticPdf->issue = 2;

        //THEN
        $sOldDir = AM_Tools::getContentPath(AM_Model_Db_StaticPdf_Data_Resource::TYPE, 1);
        $sNewDir = AM_Tools::getContentPath(AM_Model_Db_StaticPdf_Data_Resource::TYPE, 2);

        $this->_oStandardMock->expects($this->at(0))
             ->method('is_dir')
             ->with($this->equalTo($sOldDir))
             ->will($this->returnValue(true));

        $this->_oStandardMock->expects($this->at(1))
             ->method('is_dir')
             ->with($this->equalTo($sNewDir))
             ->will($this->returnValue(false));

        $this->_oStandardMock->expects($this->once())
             ->method('mkdir')
             ->with($this->equalTo($sNewDir),  $this->equalTo(0777), $this->equalTo(true))
             ->will($this->returnValue(true));

        $this->_oStandardMock->expects($this->at(3))
             ->method('copy')
             ->with($this->equalTo($sOldDir . DIRECTORY_SEPARATOR . '1.pdfmock'),
                    $this->equalTo($sNewDir . DIRECTORY_SEPARATOR . '2.pdfmock'))
             ->will($this->returnValue(true));

        //WHEN
        $oResource->copy();
    }
}
