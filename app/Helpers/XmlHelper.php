<?php

namespace App\Helpers;

use Spatie\ArrayToXml\ArrayToXml;
use Illuminate\Support\Facades\Response;

class XmlHelper
{
    /**
     * Convert array to XML response using spatie/array-to-xml
     */
    public static function toXml($data, $rootElement = 'response', $status = 200, array $headers = [])
    {
        // Using the installed package
        $xml = ArrayToXml::convert($data, $rootElement, true, 'UTF-8');
        
        $headers['Content-Type'] = 'application/xml; charset=utf-8';
        
        return Response::make($xml, $status, $headers);
    }

    /**
     * Convert XML request to array
     */
    public static function toArray($xmlContent)
    {
        // Remove BOM if present
        $xmlContent = preg_replace('/^\xEF\xBB\xBF/', '', $xmlContent);
        
        // Load XML
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($xmlContent, 'SimpleXMLElement', LIBXML_NOCDATA);
        
        if ($xml === false) {
            $errors = libxml_get_errors();
            libxml_clear_errors();
            throw new \Exception('Invalid XML: ' . print_r($errors, true));
        }
        
        $json = json_encode($xml);
        return json_decode($json, true);
    }

    /**
     * Check if request wants XML
     */
    public static function wantsXml($request)
    {
        $accept = $request->header('Accept', '');
        $contentType = $request->header('Content-Type', '');
        
        return str_contains($accept, 'application/xml') || 
               str_contains($contentType, 'application/xml') ||
               $request->is('*.xml') ||
               ($request->has('format') && $request->get('format') === 'xml');
    }

    /**
     * Check if request contains XML
     */
    public static function isXml($request)
    {
        $contentType = $request->header('Content-Type', '');
        return str_contains($contentType, 'application/xml') || 
               str_contains($contentType, 'text/xml');
    }
}