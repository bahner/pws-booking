<?php

/*
FIXME: This is a prototype member definition solely for testing purposes.

It requires the wpgraphql plugin to be activated.

https://www.wpgraphql.com/

*/

add_action('graphql_register_types', 'register_member_type');

$member = array(
);

function register_member_type() {
  register_graphql_object_type('Member', [
    'description' => __( 'Medlem i OPK', 'pws' ),
    'fields' => [
      'name' => [
        'type' => 'String',
        'description' => __('Members name', 'pws'),
      ],
      'glider' => [
        'type' => 'String',
        'description' => __('Brand and name of glider', 'pws'),
      ],
    ],
  ] );
}

add_action ( 'graphql_register_types', 'register_member_field' );

function register_member_field() {

  register_graphql_field( 'RootQuery', 'getMember', [
    'description' => 'Get a Member',
    'type' => 'Member',
    'resolve' => function() {
      return [
        'name'   => 'Titti',
        'glider' => 'Swing Mistral'
      ];
    }
  ]);
};
?>
