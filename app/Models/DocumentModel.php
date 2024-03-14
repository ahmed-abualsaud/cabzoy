<?php

namespace App\Models;

use App\Entities\Document;

class DocumentModel extends BaseModel
{
	protected $useSoftDeletes = true;
	protected $with           = 'users';
	protected $table          = 'documents';
	protected $returnType     = Document::class;
	protected $allowedFields  = [
		'user_id', 'document_title', 'document_number', 'document_front_image', 'document_back_image', 'document_comment', 'document_status',
	];
	protected $validationRules = [
		'id'              => 'permit_empty|is_natural_no_zero',
		'document_status' => 'in_list[approved, pending, rejected]',
	];
}
