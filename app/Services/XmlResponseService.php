<?php

namespace App\Services;

use App\Models\Decision;
use Illuminate\Pagination\LengthAwarePaginator;

class XmlResponseService
{
    /**
     * Convert a paginated list of decisions to XML.
     */
    public function formatDecisionsList(LengthAwarePaginator $paginator): string
    {
        $xml = new \XMLWriter();
        $xml->openMemory();
        $xml->setIndent(true);
        $xml->setIndentString('  ');
        $xml->startDocument('1.0', 'UTF-8');
        
        $xml->startElement('dazo');
        
        // Pagination metadata
        $xml->startElement('metadata');
        $xml->writeElement('total', (string)$paginator->total());
        $xml->writeElement('per_page', (string)$paginator->perPage());
        $xml->writeElement('current_page', (string)$paginator->currentPage());
        $xml->writeElement('last_page', (string)$paginator->lastPage());
        $xml->endElement(); // metadata

        $xml->startElement('decisions');

        /** @var Decision $decision */
        foreach ($paginator->items() as $decision) {
            $this->writeDecisionNode($xml, $decision, false);
        }

        $xml->endElement(); // decisions
        $xml->endElement(); // dazo
        $xml->endDocument();

        return $xml->outputMemory();
    }

    /**
     * Convert a single decision to XML (with full content).
     */
    public function formatDecisionDetail(Decision $decision): string
    {
        $xml = new \XMLWriter();
        $xml->openMemory();
        $xml->setIndent(true);
        $xml->setIndentString('  ');
        $xml->startDocument('1.0', 'UTF-8');
        
        $xml->startElement('dazo');
        $this->writeDecisionNode($xml, $decision, true);
        $xml->endElement(); // dazo
        $xml->endDocument();

        return $xml->outputMemory();
    }

    /**
     * Internal method to write a decision node.
     */
    private function writeDecisionNode(\XMLWriter $xml, Decision $decision, bool $includeContent): void
    {
        $xml->startElement('decision');
        $xml->writeAttribute('id', $decision->id);
        
        $xml->writeElement('title', $decision->title);
        $xml->writeElement('status', $decision->status);
        $xml->writeElement('visibility', $decision->visibility);
        $xml->writeElement('created_at', $decision->created_at->toIso8601String());
        $xml->writeElement('updated_at', $decision->updated_at->toIso8601String());

        if ($decision->current_deadline) {
            $xml->writeElement('deadline', clone($decision->current_deadline)->toIso8601String());
        }

        // Circle
        if ($decision->circle) {
            $xml->startElement('circle');
            $xml->writeAttribute('id', $decision->circle->id);
            $xml->writeElement('name', $decision->circle->name);
            $xml->endElement();
        }

        // Categories
        if ($decision->categories && $decision->categories->isNotEmpty()) {
            $xml->startElement('categories');
            foreach ($decision->categories as $category) {
                $xml->startElement('category');
                $xml->writeAttribute('id', $category->id);
                $xml->writeElement('name', $category->name);
                $xml->endElement();
            }
            $xml->endElement();
        }

        // Full Content (from current version)
        if ($includeContent && $decision->currentVersion) {
            $xml->startElement('content');
            // Write HTML safely as CDATA
            $xml->writeCdata($decision->currentVersion->content);
            $xml->endElement();
        }

        $xml->endElement(); // decision
    }
}
