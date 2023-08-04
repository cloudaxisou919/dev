<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: google/iam/v1/policy.proto

namespace GPBMetadata\Google\Iam\V1;

class Policy
{
    public static $is_initialized = false;

    public static function initOnce() {
        $pool = \Google\Protobuf\Internal\DescriptorPool::getGeneratedPool();

        if (static::$is_initialized == true) {
          return;
        }
        \GPBMetadata\Google\Type\Expr::initOnce();
        $pool->internalAddGeneratedFile(
            '
�	
google/iam/v1/policy.protogoogle.iam.v1"�
Policy
version ((
bindings (2.google.iam.v1.Binding1
audit_configs (2.google.iam.v1.AuditConfig
etag ("N
Binding
role (	
members (	$
	condition (2.google.type.Expr"X
AuditConfig
service (	8
audit_log_configs (2.google.iam.v1.AuditLogConfig"�
AuditLogConfig7
log_type (2%.google.iam.v1.AuditLogConfig.LogType
exempted_members (	"R
LogType
LOG_TYPE_UNSPECIFIED 

ADMIN_READ

DATA_WRITE
	DATA_READ"�
PolicyDelta3
binding_deltas (2.google.iam.v1.BindingDelta<
audit_config_deltas (2.google.iam.v1.AuditConfigDelta"�
BindingDelta2
action (2".google.iam.v1.BindingDelta.Action
role (	
member (	$
	condition (2.google.type.Expr"5
Action
ACTION_UNSPECIFIED 
ADD

REMOVE"�
AuditConfigDelta6
action (2&.google.iam.v1.AuditConfigDelta.Action
service (	
exempted_member (	
log_type (	"5
Action
ACTION_UNSPECIFIED 
ADD

REMOVEB|
com.google.iam.v1BPolicyProtoPZ)cloud.google.com/go/iam/apiv1/iampb;iampb��Google.Cloud.Iam.V1�Google\\Cloud\\Iam\\V1bproto3'
        , true);

        static::$is_initialized = true;
    }
}

