<?php

declare(strict_types=1);

namespace PixelCoda\TYPO3StorybookApi\Middleware;

use InnoHub\KhsbWebsite\Utility\DirectoryLoader;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class FluidRenderingMiddleware implements MiddlewareInterface
{
    private readonly LoggerInterface $logger;

    public function __construct(private readonly ResponseFactoryInterface $responseFactory)
    {
        $this->logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(self::class);
    }

    #[\Override]
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->logger->info('Processing middleware for path', ['path' => $request->getUri()->getPath()]);

        if ($request->getMethod() === 'OPTIONS') {
            return $this->addCorsHeaders($this->responseFactory->createResponse(200));
        }

        if ($request->getUri()->getPath() !== '/fluid/render') {
            return $this->addCorsHeaders($handler->handle($request));
        }

        $body = (string)$request->getBody();
        if ($body === '') {
            return $this->addCorsHeaders($this->createErrorResponse(400, 'Request body is invalid or empty'));
        }

        /** @var array<string, mixed>|null $jsonData */
        $jsonData = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

        if (! is_array($jsonData)) {
            return $this->addCorsHeaders($this->createErrorResponse(400, 'Invalid JSON data'));
        }

        $templatePath = $jsonData['templatePath'] ?? '';
        $section = $jsonData['section'] ?? '';
        $layout = $jsonData['layout'] ?? '';
        $variables = $jsonData['variables'] ?? [];

        if (! is_string($templatePath) || empty($templatePath)) {
            $this->logger->log(LogLevel::ERROR, 'Template path is missing or invalid');
            return $this->addCorsHeaders($this->createErrorResponse(400, 'Template path is required and must be a string'));
        }

        if (! is_array($variables)) {
            return $this->addCorsHeaders($this->createErrorResponse(400, 'Variables must be an array'));
        }

        try {
            $extensionKey = $this->getExtensionKeyFromComposer();
            $basePath = "EXT:{$extensionKey}/Resources/Private/";
            $allPaths = DirectoryLoader::getAllSubdirectories($basePath);

            $absoluteTemplatePath = GeneralUtility::getFileAbsFileName($templatePath);
            if (! file_exists($absoluteTemplatePath)) {
                $this->logger->log(LogLevel::ERROR, 'Template file not found', [
                    'requestedPath' => $templatePath,
                    'resolvedPath' => $absoluteTemplatePath,
                ]);
                return $this->addCorsHeaders($this->createErrorResponse(404, 'Template file not found'));
            }

            $view = GeneralUtility::makeInstance(StandaloneView::class);
            $view->setTemplatePathAndFilename($absoluteTemplatePath);
            $view->setLayoutRootPaths($allPaths);
            $view->setPartialRootPaths($allPaths);

            $this->logger->info('Dynamically loaded paths', ['paths' => $allPaths]);

            $GLOBALS['TYPO3_REQUEST'] = $request;

            $view->assignMultiple($variables);

            if (! empty($layout) && is_string($layout)) {
                $view->setLayoutRootPaths([$basePath . 'Layouts']);
            }

            $html = ! empty($section) && is_string($section)
                ? $view->renderSection($section, $variables)
                : $view->render();

            $this->logger->info('Template rendered successfully');
            return $this->addCorsHeaders($this->createSuccessResponse(['html' => $html]));
        } catch (\Exception $e) {
            $this->logger->log(LogLevel::ERROR, 'Error rendering template', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return $this->addCorsHeaders($this->createErrorResponse(500, 'An error occurred: ' . $e->getMessage()));
        }
    }

    private function getExtensionKeyFromComposer(): string
    {
        $composerFile = GeneralUtility::getFileAbsFileName('EXT:khsb_website/composer.json');
        if (! file_exists($composerFile)) {
            throw new \RuntimeException('composer.json file not found');
        }

        $composerData = json_decode((string)file_get_contents($composerFile), true, 512, JSON_THROW_ON_ERROR);
        if (! is_array($composerData)) {
            throw new \RuntimeException('Invalid composer.json format');
        }

        $extra = $composerData['extra'] ?? null;
        if (! is_array($extra) || ! isset($extra['typo3/cms']) || ! is_array($extra['typo3/cms'])) {
            throw new \RuntimeException('Invalid or missing TYPO3 configuration in composer.json');
        }

        $extensionKey = $extra['typo3/cms']['extension-key'] ?? null;
        if (! is_string($extensionKey)) {
            throw new \RuntimeException('Extension key not defined in composer.json');
        }

        return $extensionKey;
    }

    private function addCorsHeaders(ResponseInterface $response): ResponseInterface
    {
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Methods', 'POST, GET, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    }

    /**
     * @param array<string, mixed> $data
     */
    private function createSuccessResponse(array $data): ResponseInterface
    {
        $response = $this->responseFactory->createResponse();
        $response->getBody()->write(json_encode($data, JSON_THROW_ON_ERROR));
        return $response->withHeader('Content-Type', 'application/json');
    }

    private function createErrorResponse(int $statusCode, string $errorMessage): ResponseInterface
    {
        $response = $this->responseFactory->createResponse($statusCode);
        $response->getBody()->write(json_encode(['error' => $errorMessage], JSON_THROW_ON_ERROR));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
