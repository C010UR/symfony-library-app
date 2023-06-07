<?php

namespace App\Utils;

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

class RequestUtils
{
    public static function submitForm(
        Request $request,
        FormInterface $form,
        bool $isClearMissing = false,
    ): FormInterface {
        $contentType = strtolower($request->headers->get('content-type'));

        $handles = [
            [
                'content-type' => ['application/json'],
                'handle' => static function () use ($request) : array {
                    try {
                        return json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
                    } catch (\Throwable $throwable) {
                        throw new NotEncodableValueException('Json is invalid.', previous: $throwable);
                    }
                },
            ],
            [
                'content-type' => ['multipart/form-data', 'application/x-www-form-urlencoded'],
                'handle' => static fn(): array => array_merge($request->request->all(), $request->files->all()),
            ],
        ];

        foreach ($handles as $handle) {
            foreach ($handle['content-type'] as $supportedContentType) {
                if (str_contains($contentType, $supportedContentType)) {
                    $form->submit($handle['handle'](), $isClearMissing);

                    if (!$form->isValid()) {
                        $errors = [];

                        /** @var FormError $error */
                        foreach ($form->getErrors(true) as $error) {
                            $errors[] = $error->getMessage();
                        }

                        throw new BadRequestException(implode('<br>', $errors));
                    }

                    return $form;
                }
            }
        }

        throw new BadRequestException(sprintf("Content-Type '%s' is not supported.", $request->headers->get('content-type')));
    }
}
