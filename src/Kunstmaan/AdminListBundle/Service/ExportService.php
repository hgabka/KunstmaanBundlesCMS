<?php

namespace Kunstmaan\AdminListBundle\Service;

use Box\Spout\Common\Type;
use Box\Spout\Writer\WriterFactory;
use Kunstmaan\AdminListBundle\AdminList\ExportableInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Translation\Translator;

class ExportService
{
    const EXT_CSV = 'csv';
    const EXT_EXCEL = 'xlsx';
    /**
     * @var EngineInterface
     */
    private $renderer;

    /**
     * @var Translator
     */
    private $translator;

    /**
     * @return array
     */
    public static function getSupportedExtensions()
    {
        $rfl = new \ReflectionClass(new self());
        $data = $rfl->getConstants();

        $extensions = [];
        foreach ($data as $name => $ext) {
            if (false !== strpos($name, 'EXT_')) {
                $key = ucfirst(strtolower(str_replace('EXT_', '', $name)));
                $extensions[$key] = $ext;
            }
        }

        return $extensions;
    }

    /**
     * @param ExportableInterface $adminList
     * @param string              $_format
     * @param null|string         $template
     *
     * @throws \Exception
     *
     * @return Response|StreamedResponse
     */
    public function getDownloadableResponse(ExportableInterface $adminList, $_format, $template = null)
    {
        switch ($_format) {
            case self::EXT_EXCEL:
                $response = $this->streamExcelSheet($adminList);

                break;
            default:
                $content = $this->createFromTemplate($adminList, $_format, $template);
                $response = $this->createResponse($content, $_format);
                $filename = sprintf('entries.%s', $_format);
                $response->headers->set('Content-Disposition', sprintf('attachment; filename=%s', $filename));

                break;
        }

        return $response;
    }

    /**
     * @param ExportableInterface $adminList
     * @param string              $_format
     * @param null|string         $template
     *
     * @return string
     */
    public function createFromTemplate(ExportableInterface $adminList, $_format, $template = null)
    {
        if (null === $template) {
            $template = sprintf('KunstmaanAdminListBundle:Default:export.%s.twig', $_format);
        }

        $iterator = $adminList->getIterator();

        return $this->renderer->render(
            $template,
            [
                'iterator' => $iterator,
                'adminlist' => $adminList,
                'queryparams' => [],
            ]
        );
    }

    /**
     * @param ExportableInterface $adminList
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function streamExcelSheet(ExportableInterface $adminList)
    {
        $response = new StreamedResponse();
        $response->setCallback(function () use ($adminList) {
            $writer = WriterFactory::create(Type::XLSX);
            $writer->openToBrowser('export.xlsx');

            $row = [];
            foreach ($adminList->getExportColumns() as $column) {
                $row[] = $this->translator->trans($column->getHeader());
            }
            $writer->addRow($row);

            $iterator = $adminList->getIterator();
            $rows = [];
            foreach ($iterator as $item) {
                if (array_key_exists(0, $item)) {
                    $itemObject = $item[0];
                } else {
                    $itemObject = $item;
                }

                $row = [];
                foreach ($adminList->getExportColumns() as $column) {
                    $columnName = $column->getName();
                    $itemHelper = $itemObject;
                    if ($column->hasAlias()) {
                        $itemHelper = $column->getAliasObj($itemObject);
                        $columnName = $column->getColumnName($columnName);
                    }
                    $data = $adminList->getStringValue($itemHelper, $columnName);
                    if (null !== $column->getTemplate()) {
                        if (!$this->renderer->exists($column->getTemplate())) {
                            throw new \Exception('No export template defined for '.get_class($data));
                        }

                        $data = $this->renderer->render($column->getTemplate(), ['object' => $data]);
                    }

                    $row[] = $data;
                }
                $rows[] = $row;
            }

            $writer->addRows($rows);
            $writer->close();
        });

        return $response;
    }

    /**
     * @param string $content
     * @param string $_format
     *
     * @return Response
     */
    public function createResponse($content, $_format)
    {
        $response = new Response();
        $response->headers->set('Content-Type', sprintf('text/%s', $_format));
        $response->setContent($content);

        return $response;
    }

    /**
     * @param EngineInterface $renderer
     */
    public function setRenderer($renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * @param Translator $translator
     */
    public function setTranslator($translator)
    {
        $this->translator = $translator;
    }
}
