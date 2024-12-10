<?php

use Tests\Helpers\FormSubmissionDataFactory;

it('can submit form with valid table input', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace);

    $tableProperty = [
        'id' => 'table_field',
        'name' => 'table Question',
        'type' => 'table',
        'rows' => ['Row 1', 'Row 2', 'Row 3'],
        'columns' => ['Column A', 'Column B', 'Column C'],
        'required' => true
    ];

    $form->properties = array_merge($form->properties, [$tableProperty]);
    $form->update();

    $submissionData = [
        'table_field' => [
            'Row 1' => 'Column A',
            'Row 2' => 'Column B',
            'Row 3' => 'Column C'
        ]
    ];

    $formData = FormSubmissionDataFactory::generateSubmissionData($form, $submissionData);

    $this->postJson(route('forms.answer', $form->slug), $formData)
        ->assertSuccessful()
        ->assertJson([
            'type' => 'success',
            'message' => 'Form submission saved.',
        ]);
});

it('cannot submit form with invalid table input', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace);

    $tableProperty = [
        'id' => 'table_field',
        'name' => 'table Question',
        'type' => 'table',
        'rows' => ['Row 1', 'Row 2', 'Row 3'],
        'columns' => ['Column A', 'Column B', 'Column C'],
        'required' => true
    ];

    $form->properties = array_merge($form->properties, [$tableProperty]);
    $form->update();

    $submissionData = [
        'table_field' => [
            'Row 1' => 'Column A',
            'Row 2' => 'Invalid Column',
            'Row 3' => 'Column C'
        ]
    ];

    $formData = FormSubmissionDataFactory::generateSubmissionData($form, $submissionData);

    $this->postJson(route('forms.answer', $form->slug), $formData)
        ->assertStatus(422)
        ->assertJson([
            'message' => "Invalid value 'Invalid Column' for row 'Row 2'.",
            'errors' => [
                'table_field' => [
                    "Invalid value 'Invalid Column' for row 'Row 2'."
                ]
            ]
        ]);
});

it('can submit form with optional table input left empty', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace);

    $tableProperty = [
        'id' => 'table_field',
        'name' => 'table Question',
        'type' => 'table',
        'rows' => ['Row 1', 'Row 2', 'Row 3'],
        'columns' => ['Column A', 'Column B', 'Column C'],
        'required' => false
    ];

    $form->properties = array_merge($form->properties, [$tableProperty]);
    $form->update();

    $submissionData = [
        'table_field' => []
    ];

    $formData = FormSubmissionDataFactory::generateSubmissionData($form, $submissionData);

    $this->postJson(route('forms.answer', $form->slug), $formData)
        ->assertSuccessful()
        ->assertJson([
            'type' => 'success',
            'message' => 'Form submission saved.',
        ]);
});

it('cannot submit form with required table input left empty', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace);

    $tableProperty = [
        'id' => 'table_field',
        'name' => 'table Question',
        'type' => 'table',
        'rows' => ['Row 1', 'Row 2', 'Row 3'],
        'columns' => ['Column A', 'Column B', 'Column C'],
        'required' => true
    ];

    $form->properties = array_merge($form->properties, [$tableProperty]);
    $form->update();

    $submissionData = [
        'table_field' => []
    ];

    $formData = FormSubmissionDataFactory::generateSubmissionData($form, $submissionData);

    $this->postJson(route('forms.answer', $form->slug), $formData)
        ->assertStatus(422)
        ->assertJson([
            'message' => 'The table Question field is required.',
            'errors' => [
                'table_field' => [
                    'The table Question field is required.'
                ]
            ]
        ]);
});

it('can validate table input with precognition', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace);

    $tableProperty = [
        'id' => 'table_field',
        'name' => 'table Question',
        'type' => 'table',
        'rows' => ['Row 1', 'Row 2', 'Row 3'],
        'columns' => ['Column A', 'Column B', 'Column C'],
        'required' => true
    ];

    $form->properties = array_merge($form->properties, [$tableProperty]);
    $form->update();

    $submissionData = [
        'table_field' => [
            'Row 1' => 'Column A',
            'Row 2' => 'Invalid Column',
            'Row 3' => 'Column C'
        ]
    ];

    $formData = FormSubmissionDataFactory::generateSubmissionData($form, $submissionData);

    $response = $this->withPrecognition()->withHeaders([
        'Precognition-Validate-Only' => 'table_field'
    ])
        ->postJson(route('forms.answer', $form->slug), $formData);

    $response->assertStatus(422)
        ->assertJson([
            'errors' => [
                'table_field' => [
                    'Invalid value \'Invalid Column\' for row \'Row 2\'.'
                ]
            ]
        ]);
});
