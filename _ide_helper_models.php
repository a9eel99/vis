<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Domain\Models{
/**
 * @property string $id
 * @property string|null $user_id
 * @property string $action
 * @property string $model_type
 * @property string|null $model_id
 * @property array<array-key, mixed>|null $old_values
 * @property array<array-key, mixed>|null $new_values
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent|null $model
 * @property-read \App\Domain\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereNewValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereOldValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereUserId($value)
 */
	class AuditLog extends \Eloquent {}
}

namespace App\Domain\Models{
/**
 * @property string $id
 * @property string $name
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $id_number
 * @property string|null $address
 * @property string|null $notes
 * @property string|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Domain\Models\User|null $creator
 * @property-read string|null $whatsapp_link
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Domain\Models\Inspection> $inspections
 * @property-read int|null $inspections_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Domain\Models\Vehicle> $vehicles
 * @property-read int|null $vehicles_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereIdNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer withoutTrashed()
 */
	class Customer extends \Eloquent {}
}

namespace App\Domain\Models{
/**
 * @property string $id
 * @property string $reference_number
 * @property string $vehicle_id
 * @property string $template_id
 * @property string|null $inspector_id
 * @property string|null $created_by
 * @property \App\Domain\Enums\InspectionStatus $status
 * @property numeric|null $total_score
 * @property numeric|null $percentage
 * @property string|null $grade
 * @property bool $has_critical_failure
 * @property string|null $share_token
 * @property bool $is_hidden
 * @property string|null $hidden_reason
 * @property \Illuminate\Support\Carbon|null $hidden_at
 * @property string|null $hidden_by
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $started_at
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Domain\Models\User|null $creator
 * @property-read string $grade_color
 * @property-read \App\Domain\Enums\InspectionGrade|null $grade_enum
 * @property-read string $grade_label
 * @property-read string $status_color
 * @property-read string $status_label
 * @property-read \App\Domain\Models\User|null $hiddenByUser
 * @property-read \App\Domain\Models\User|null $inspector
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Domain\Models\InspectionMedia> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Domain\Models\InspectionResult> $results
 * @property-read int|null $results_count
 * @property-read \App\Domain\Models\InspectionTemplate $template
 * @property-read \App\Domain\Models\Vehicle $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inspection byInspector($inspectorId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inspection completed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inspection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inspection newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inspection onlyHidden()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inspection onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inspection query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inspection thisMonth()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inspection whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inspection whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inspection whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inspection whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inspection whereGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inspection whereHasCriticalFailure($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inspection whereHiddenAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inspection whereHiddenBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inspection whereHiddenReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inspection whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inspection whereInspectorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inspection whereIsHidden($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inspection whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inspection wherePercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inspection whereReferenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inspection whereShareToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inspection whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inspection whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inspection whereTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inspection whereTotalScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inspection whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inspection whereVehicleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inspection withHidden()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inspection withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inspection withoutTrashed()
 */
	class Inspection extends \Eloquent {}
}

namespace App\Domain\Models{
/**
 * @property string $id
 * @property string $inspection_id
 * @property string|null $result_id
 * @property string|null $question_id
 * @property string $type
 * @property string $filename
 * @property string $original_name
 * @property string $path
 * @property string $mime_type
 * @property int $size
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $human_size
 * @property-read string $url
 * @property-read \App\Domain\Models\Inspection $inspection
 * @property-read \App\Domain\Models\InspectionQuestion|null $question
 * @property-read \App\Domain\Models\InspectionResult|null $result
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionMedia newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionMedia newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionMedia query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionMedia whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionMedia whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionMedia whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionMedia whereInspectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionMedia whereMimeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionMedia whereOriginalName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionMedia wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionMedia whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionMedia whereResultId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionMedia whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionMedia whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionMedia whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionMedia whereUpdatedAt($value)
 */
	class InspectionMedia extends \Eloquent {}
}

namespace App\Domain\Models{
/**
 * @property string $id
 * @property string $section_id
 * @property string $label
 * @property string|null $description
 * @property \App\Domain\Enums\QuestionType $type
 * @property array<array-key, mixed>|null $options
 * @property numeric $weight
 * @property numeric $max_score
 * @property bool $is_critical
 * @property bool $is_required
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read float $weighted_max_score
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Domain\Models\InspectionMedia> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Domain\Models\InspectionResult> $results
 * @property-read int|null $results_count
 * @property-read \App\Domain\Models\InspectionSection $section
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionQuestion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionQuestion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionQuestion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionQuestion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionQuestion whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionQuestion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionQuestion whereIsCritical($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionQuestion whereIsRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionQuestion whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionQuestion whereMaxScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionQuestion whereOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionQuestion whereSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionQuestion whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionQuestion whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionQuestion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionQuestion whereWeight($value)
 */
	class InspectionQuestion extends \Eloquent {}
}

namespace App\Domain\Models{
/**
 * @property string $id
 * @property string $inspection_id
 * @property string $question_id
 * @property string|null $answer
 * @property numeric|null $score
 * @property bool $is_critical_fail
 * @property string|null $remarks
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read float $weighted_score
 * @property-read \App\Domain\Models\Inspection $inspection
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Domain\Models\InspectionMedia> $media
 * @property-read int|null $media_count
 * @property-read \App\Domain\Models\InspectionQuestion $question
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionResult newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionResult newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionResult query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionResult whereAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionResult whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionResult whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionResult whereInspectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionResult whereIsCriticalFail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionResult whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionResult whereRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionResult whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionResult whereUpdatedAt($value)
 */
	class InspectionResult extends \Eloquent {}
}

namespace App\Domain\Models{
/**
 * @property string $id
 * @property string $template_id
 * @property string $name
 * @property string|null $description
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Domain\Models\InspectionQuestion> $questions
 * @property-read int|null $questions_count
 * @property-read \App\Domain\Models\InspectionTemplate $template
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionSection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionSection newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionSection query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionSection whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionSection whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionSection whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionSection whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionSection whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionSection whereTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionSection whereUpdatedAt($value)
 */
	class InspectionSection extends \Eloquent {}
}

namespace App\Domain\Models{
/**
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property bool $is_active
 * @property string $scoring_mode scored = with grades/percentages, descriptive = observations only
 * @property string|null $fuel_type
 * @property int $version
 * @property string|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Domain\Models\User|null $creator
 * @property-read float $max_possible_score
 * @property-read string $scoring_mode_label
 * @property-read int $total_questions
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Domain\Models\Inspection> $inspections
 * @property-read int|null $inspections_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Domain\Models\InspectionQuestion> $questions
 * @property-read int|null $questions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Domain\Models\InspectionSection> $sections
 * @property-read int|null $sections_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionTemplate onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionTemplate whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionTemplate whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionTemplate whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionTemplate whereFuelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionTemplate whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionTemplate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionTemplate whereScoringMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionTemplate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionTemplate whereVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionTemplate withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionTemplate withoutTrashed()
 */
	class InspectionTemplate extends \Eloquent {}
}

namespace App\Domain\Models{
/**
 * @property string $key
 * @property string|null $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereValue($value)
 */
	class Setting extends \Eloquent {}
}

namespace App\Domain\Models{
/**
 * @property string $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $phone
 * @property string|null $avatar
 * @property bool $is_active
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Domain\Models\Inspection> $createdInspections
 * @property-read int|null $created_inspections_count
 * @property-read string $initials
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Domain\Models\Inspection> $inspections
 * @property-read int|null $inspections_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Domain\Models\Vehicle> $vehicles
 * @property-read int|null $vehicles_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTrashed()
 */
	class User extends \Eloquent {}
}

namespace App\Domain\Models{
/**
 * @property string $id
 * @property string|null $customer_id
 * @property string $make
 * @property string $model
 * @property int $year
 * @property string|null $vin
 * @property string|null $license_plate
 * @property string|null $color
 * @property int|null $mileage
 * @property string|null $fuel_type
 * @property string|null $transmission
 * @property string|null $owner_name
 * @property string|null $owner_phone
 * @property string|null $owner_email
 * @property string|null $notes
 * @property string|null $image
 * @property string|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Domain\Models\User|null $creator
 * @property-read \App\Domain\Models\Customer|null $customer
 * @property-read string $full_name
 * @property-read int $inspection_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Domain\Models\Inspection> $inspections
 * @property-read int|null $inspections_count
 * @property-read \App\Domain\Models\Inspection|null $latestInspection
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereFuelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereLicensePlate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereMake($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereMileage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereOwnerEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereOwnerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereOwnerPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereTransmission($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereVin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle withoutTrashed()
 */
	class Vehicle extends \Eloquent {}
}

