<?php
// tests/DeleteFileTest.php
use PHPUnit\Framework\TestCase;

class delete_pdfTest extends TestCase
{
    protected $testFilePath;

    protected function setUp(): void
    {
        $this->testFilePath = __DIR__ . '/../pdf_storage1/Demande_AA1111.pdf';
        
        // Créer un fichier de test
        file_put_contents($this->testFilePath, 'test pdf content');
    }

    protected function tearDown(): void
    {
        // Nettoyer après les tests
        if (file_exists($this->testFilePath)) {
            unlink($this->testFilePath);
        }
    }

    public function testDeleteExistingFile()
    {
        require_once __DIR__ . '/../ADMINISTRATEUR/delete_pdf.php';
        
        $filename = 'Demande_AA1111.pdf';
        $result = deleteFile($filename);

        $this->assertEquals("Le fichier $filename a été supprimé.", $result);
        $this->assertFileDoesNotExist($this->testFilePath);
    }

    public function testDeleteNonExistingFile()
    {
        require_once __DIR__ . '/../ADMINISTRATEUR/delete_pdf.php';

        $filename = 'nonexistingfile.pdf';
        $result = deleteFile($filename);

        $this->assertEquals("Le fichier $filename n'existe pas.", $result);
    }

    public function testDeleteFileWithoutFilenameParameter()
    {
        require_once __DIR__ . '/../ADMINISTRATEUR/delete_pdf.php';

        // Simuler l'absence du paramètre de fichier
        // Call the deleteFile function with no filename
        $result = deleteFile(); 

        $this->assertEquals("Paramètre de fichier manquant.", $result);
    }
}
?>