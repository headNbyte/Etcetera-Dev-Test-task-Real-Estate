<?php
/**
 * Register ACF fields for Real Estate Objects
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Check if ACF is active
if (!function_exists('acf_add_local_field_group')) {
    return;
}

// Hook into ACF initialization
add_action('acf/init', 'real_estate_objects_register_acf_fields');

function real_estate_objects_register_acf_fields() {
    acf_add_local_field_group(array(
        'key' => 'group_real_estate_objects',
        'title' => 'Інформація про об\'єкт нерухомості',
        'fields' => array(
            array(
                'key' => 'field_building_name',
                'label' => 'Назва будинку',
                'name' => 'building_name',
                'type' => 'text',
                'required' => 1,
                'placeholder' => 'Введіть назву будинку',
            ),
            array(
                'key' => 'field_coordinates',
                'label' => 'Координати місцезнаходження',
                'name' => 'coordinates',
                'type' => 'text',
                'required' => 0,
                'placeholder' => 'Наприклад: 50.4501, 30.5234',
            ),
            array(
                'key' => 'field_floors',
                'label' => 'Кількість поверхів',
                'name' => 'floors',
                'type' => 'select',
                'required' => 1,
                'choices' => array_combine(range(1, 20), range(1, 20)),
                'default_value' => 1,
                'allow_null' => 0,
                'multiple' => 0,
                'ui' => 1,
                'return_format' => 'value',
            ),
            array(
                'key' => 'field_building_type',
                'label' => 'Тип будівлі',
                'name' => 'building_type',
                'type' => 'radio',
                'required' => 1,
                'choices' => array(
                    'panel' => 'Панель',
                    'brick' => 'Цегла',
                    'foam_block' => 'Піноблок',
                ),
                'default_value' => 'brick',
                'layout' => 'horizontal',
                'return_format' => 'value',
            ),
            array(
                'key' => 'field_eco_rating',
                'label' => 'Екологічність',
                'name' => 'eco_rating',
                'type' => 'select',
                'required' => 1,
                'choices' => array(
                    '1' => '1 - Низька',
                    '2' => '2',
                    '3' => '3 - Середня',
                    '4' => '4',
                    '5' => '5 - Висока',
                ),
                'default_value' => '3',
                'allow_null' => 0,
                'multiple' => 0,
                'ui' => 1,
                'return_format' => 'value',
            ),
            array(
                'key' => 'field_building_image',
                'label' => 'Зображення',
                'name' => 'building_image',
                'type' => 'image',
                'required' => 0,
                'return_format' => 'array',
                'preview_size' => 'medium',
                'library' => 'all',
            ),
            array(
                'key' => 'field_premises',
                'label' => 'Приміщення',
                'name' => 'premises',
                'type' => 'repeater',
                'required' => 0,
                'min' => 0,
                'max' => 0,
                'layout' => 'row',
                'collapsed' => 'field_premise_area',
                'sub_fields' => array(
                    array(
                        'key' => 'field_premise_area',
                        'label' => 'Площа',
                        'name' => 'area',
                        'type' => 'text',
                        'required' => 1,
                        'placeholder' => 'м²',
                    ),
                    array(
                        'key' => 'field_premise_rooms',
                        'label' => 'Кількість кімнат',
                        'name' => 'rooms',
                        'type' => 'radio',
                        'required' => 1,
                        'choices' => array_combine(range(1, 10), range(1, 10)),
                        'default_value' => 1,
                        'layout' => 'horizontal',
                        'return_format' => 'value',
                    ),
                    array(
                        'key' => 'field_premise_balcony',
                        'label' => 'Балкон',
                        'name' => 'balcony',
                        'type' => 'radio',
                        'required' => 1,
                        'choices' => array(
                            'yes' => 'Так',
                            'no' => 'Ні',
                        ),
                        'default_value' => 'no',
                        'layout' => 'horizontal',
                        'return_format' => 'value',
                    ),
                    array(
                        'key' => 'field_premise_bathroom',
                        'label' => 'Санвузол',
                        'name' => 'bathroom',
                        'type' => 'radio',
                        'required' => 1,
                        'choices' => array(
                            'yes' => 'Так',
                            'no' => 'Ні',
                        ),
                        'default_value' => 'yes',
                        'layout' => 'horizontal',
                        'return_format' => 'value',
                    ),
                    array(
                        'key' => 'field_premise_image',
                        'label' => 'Зображення',
                        'name' => 'image',
                        'type' => 'image',
                        'required' => 0,
                        'return_format' => 'array',
                        'preview_size' => 'medium',
                        'library' => 'all',
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'real_estate_object',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
    ));
}
