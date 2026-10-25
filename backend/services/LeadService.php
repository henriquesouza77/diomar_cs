<?php
declare(strict_types=1);

namespace Services;

use Core\RateLimit;
use Core\Request;
use Core\Response;
use Models\Lead;
use Models\Property;
use Middleware\AuthMiddleware;

class LeadService
{
    public static function create(array $data): array
    {
        // Campo-isca: invisível para pessoas, preenchido por robôs. Finge sucesso e descarta.
        if (!empty($data['website'])) {
            return ['id' => 0];
        }

        $ip = Request::ip();
        RateLimit::enforce('lead_ip', $ip, 5, 600);
        RateLimit::hit('lead_ip', $ip);

        $name     = clean_str($data['name'] ?? '', 150);
        $email    = clean_email($data['email'] ?? '');
        $phone    = clean_phone($data['phone'] ?? null);
        $message  = clean_text($data['message'] ?? '', 2000);

        $errors = [];
        if (!$name)  $errors['name'] = 'Nome obrigatório.';
        if (!$email) $errors['email'] = 'E-mail inválido.';
        if ($errors) Response::error('Erro de validação.', 422, ['fields' => $errors]);

        $propertyId = !empty($data['property_id']) ? (int)$data['property_id'] : null;
        $property = null;
        if ($propertyId !== null) {
            $property = Property::find($propertyId);
            if (!$property || !(bool)$property['published']) {
                Response::error('Imóvel não encontrado.', 404);
            }
        }

        $leadId = Lead::create([
            'property_id' => $propertyId,
            'agent_id'    => $property['agent_id'] ?? null,
            'name'        => $name,
            'email'       => $email,
            'phone'       => $phone,
            'whatsapp'    => $phone,
            'message'     => $message,
            'origin'      => in_array($data['origin'] ?? '', ['site', 'whatsapp', 'anunciar'], true) ? $data['origin'] : 'site',
        ]);

        ActivityLogService::log('lead.create', 'lead', $leadId, ['name' => $name]);
        return ['id' => $leadId];
    }

    public static function list(array $filters): array
    {
        AuthMiddleware::requireAgent();
        $page = max(1, (int)($filters['page'] ?? 1));

        // Corretor vê só os leads dele e os ainda sem dono; admin vê todos
        if (AuthMiddleware::userRole() === ROLE_AGENT) {
            $filters['agent_scope'] = AuthMiddleware::userId();
        }
        return Lead::listAll($filters, $page, 20);
    }

    public static function updateStatus(int $id, int $statusId): void
    {
        AuthMiddleware::requireAgent();
        if (!in_array($statusId, [1, 2, 3, 4, 5], true)) {
            Response::error('Status inválido.', 422);
        }

        $lead = Lead::find($id);
        if (!$lead) Response::error('Lead não encontrado.', 404);
        if (AuthMiddleware::userRole() === ROLE_AGENT
            && $lead['agent_id'] !== null
            && (int) $lead['agent_id'] !== AuthMiddleware::userId()) {
            Response::error('Este lead é de outro corretor.', 403);
        }

        Lead::updateStatus($id, $statusId);
        ActivityLogService::log('lead.update_status', 'lead', $id, ['status_id' => $statusId]);
    }
}