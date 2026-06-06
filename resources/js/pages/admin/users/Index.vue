<script setup lang="ts">
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import {
    Check,
    Clipboard,
    KeyRound,
    LockKeyhole,
    Pencil,
    Plus,
    Search,
    ShieldCheck,
    SlidersHorizontal,
    Trash2,
    UserRound,
    UsersRound,
} from 'lucide-vue-next';
import { computed, shallowRef, watch } from 'vue';
import { toast } from 'vue-sonner';
import AccessPagination from '@/components/admin/access/AccessPagination.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type PageMeta = {
    current_page: number;
    from: number | null;
    last_page: number;
    per_page: number;
    to: number | null;
    total: number;
};

type PageLinks = {
    first: string | null;
    prev: string | null;
    next: string | null;
    last: string | null;
};

type Paginated<T> = {
    data: T[];
    meta: PageMeta;
    links: PageLinks;
};

type ManagedUser = {
    id: number;
    name: string;
    email: string;
    nic: string | null;
    phone: string | null;
    job_title: string | null;
    status: 'active' | 'suspended';
    roles: string[];
    email_verified_at: string | null;
    last_login_at: string | null;
    created_at: string | null;
    can: {
        update: boolean;
        delete: boolean;
        suspend: boolean;
    };
};

type ManagedRole = {
    id: number;
    name: string;
    users_count: number;
    permissions: string[];
    protected: boolean;
    can: {
        update: boolean;
        delete: boolean;
    };
};

type ManagedPermission = {
    id: number;
    name: string;
    roles_count: number;
    protected: boolean;
    can: {
        update: boolean;
        delete: boolean;
    };
};

type Credentials = {
    name: string;
    email: string;
    password: string;
};

const props = defineProps<{
    users: Paginated<ManagedUser>;
    roles: Paginated<ManagedRole>;
    permissions: Paginated<ManagedPermission>;
    assignableRoles: string[];
    allPermissions: string[];
    filters: {
        user_search: string;
        status: string;
        role: string;
        role_search: string;
        permission_search: string;
        tab: string;
    };
    stats: {
        users: number;
        activeUsers: number;
        roles: number;
        permissions: number;
    };
    protected: {
        roles: string[];
        permissions: string[];
    };
}>();

const page = usePage();
const activeTab = shallowRef(props.filters.tab === 'rbac' ? 'rbac' : 'users');
const userDialogOpen = shallowRef(false);
const roleDialogOpen = shallowRef(false);
const permissionDialogOpen = shallowRef(false);
const deleteDialogOpen = shallowRef(false);
const credentialsDialogOpen = shallowRef(false);
const editingUser = shallowRef<ManagedUser | null>(null);
const editingRole = shallowRef<ManagedRole | null>(null);
const editingPermission = shallowRef<ManagedPermission | null>(null);
const deleteTarget = shallowRef<{
    type: 'user' | 'role' | 'permission';
    id: number;
    label: string;
    url: string;
} | null>(null);
const credentials = shallowRef<Credentials | null>(
    page.props.flash.createdUserCredentials ?? null,
);

const userFilters = useForm({
    user_search: props.filters.user_search,
    status: props.filters.status,
    role: props.filters.role,
    tab: 'users',
});

const roleFilters = useForm({
    role_search: props.filters.role_search,
    permission_search: props.filters.permission_search,
    tab: 'rbac',
});

const userForm = useForm({
    name: '',
    email: '',
    nic: '',
    phone: '',
    job_title: '',
    status: 'active',
    password: '',
    roles: [] as string[],
});

const roleForm = useForm({
    name: '',
    permissions: [] as string[],
});

const permissionForm = useForm({
    name: '',
});

const deleteForm = useForm({});

const userDialogTitle = computed(() =>
    editingUser.value ? 'Edit user account' : 'Add user account',
);

const roleDialogTitle = computed(() =>
    editingRole.value ? 'Edit role' : 'Create role',
);

const permissionDialogTitle = computed(() =>
    editingPermission.value ? 'Edit permission' : 'Create permission',
);

const activeUserPercentage = computed(() => {
    if (props.stats.users === 0) {
        return 0;
    }

    return Math.round((props.stats.activeUsers / props.stats.users) * 100);
});

watch(
    () => page.props.flash.createdUserCredentials,
    (value) => {
        if (!value) {
            return;
        }

        credentials.value = value;
        credentialsDialogOpen.value = true;
    },
    { immediate: true },
);

const formatDate = (value: string | null): string => {
    if (!value) {
        return 'Never';
    }

    return new Intl.DateTimeFormat('en', {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(new Date(value));
};

const generatePassword = (): string => {
    const alphabet =
        'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789';
    const symbols = ['#', '!', '@', '$'];
    const random = Array.from({ length: 14 }, () =>
        alphabet.charAt(Math.floor(Math.random() * alphabet.length)),
    ).join('');

    return `Po${random}${symbols[Math.floor(Math.random() * symbols.length)]}26`;
};

const setTab = (tab: 'users' | 'rbac') => {
    activeTab.value = tab;
    router.get(
        '/admin/users',
        { ...props.filters, tab },
        { preserveScroll: true, preserveState: true, replace: true },
    );
};

const applyUserFilters = () => {
    userFilters.get('/admin/users', {
        preserveScroll: true,
        preserveState: true,
        replace: true,
    });
};

const resetUserFilters = () => {
    userFilters.user_search = '';
    userFilters.status = '';
    userFilters.role = '';
    applyUserFilters();
};

const applyRoleFilters = () => {
    roleFilters.get('/admin/users', {
        preserveScroll: true,
        preserveState: true,
        replace: true,
    });
};

const resetRoleFilters = () => {
    roleFilters.role_search = '';
    roleFilters.permission_search = '';
    applyRoleFilters();
};

const openCreateUser = () => {
    editingUser.value = null;
    userForm.reset();
    userForm.clearErrors();
    userForm.status = 'active';
    userForm.password = generatePassword();
    userForm.roles = props.assignableRoles.includes('employee')
        ? ['employee']
        : [];
    userDialogOpen.value = true;
};

const openEditUser = (user: ManagedUser) => {
    editingUser.value = user;
    userForm.clearErrors();
    userForm.name = user.name;
    userForm.email = user.email;
    userForm.nic = user.nic ?? '';
    userForm.phone = user.phone ?? '';
    userForm.job_title = user.job_title ?? '';
    userForm.status = user.status;
    userForm.password = '';
    userForm.roles = user.roles.filter((role) =>
        props.assignableRoles.includes(role),
    );
    userDialogOpen.value = true;
};

const submitUser = () => {
    if (editingUser.value) {
        userForm.put(`/admin/users/${editingUser.value.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                userDialogOpen.value = false;
                userForm.reset();
            },
        });

        return;
    }

    userForm.post('/admin/users', {
        preserveScroll: true,
        onSuccess: () => {
            userDialogOpen.value = false;
            userForm.reset();
        },
    });
};

const openCreateRole = () => {
    editingRole.value = null;
    roleForm.reset();
    roleForm.clearErrors();
    roleDialogOpen.value = true;
};

const openEditRole = (role: ManagedRole) => {
    editingRole.value = role;
    roleForm.clearErrors();
    roleForm.name = role.name;
    roleForm.permissions = [...role.permissions];
    roleDialogOpen.value = true;
};

const submitRole = () => {
    if (editingRole.value) {
        roleForm.put(`/admin/roles/${editingRole.value.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                roleDialogOpen.value = false;
                roleForm.reset();
            },
        });

        return;
    }

    roleForm.post('/admin/roles', {
        preserveScroll: true,
        onSuccess: () => {
            roleDialogOpen.value = false;
            roleForm.reset();
        },
    });
};

const openCreatePermission = () => {
    editingPermission.value = null;
    permissionForm.reset();
    permissionForm.clearErrors();
    permissionDialogOpen.value = true;
};

const openEditPermission = (permission: ManagedPermission) => {
    editingPermission.value = permission;
    permissionForm.clearErrors();
    permissionForm.name = permission.name;
    permissionDialogOpen.value = true;
};

const submitPermission = () => {
    if (editingPermission.value) {
        permissionForm.put(`/admin/permissions/${editingPermission.value.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                permissionDialogOpen.value = false;
                permissionForm.reset();
            },
        });

        return;
    }

    permissionForm.post('/admin/permissions', {
        preserveScroll: true,
        onSuccess: () => {
            permissionDialogOpen.value = false;
            permissionForm.reset();
        },
    });
};

const confirmDelete = (
    type: 'user' | 'role' | 'permission',
    id: number,
    label: string,
) => {
    deleteTarget.value = {
        type,
        id,
        label,
        url:
            type === 'user'
                ? `/admin/users/${id}`
                : type === 'role'
                  ? `/admin/roles/${id}`
                  : `/admin/permissions/${id}`,
    };
    deleteDialogOpen.value = true;
};

const submitDelete = () => {
    if (!deleteTarget.value) {
        return;
    }

    deleteForm.delete(deleteTarget.value.url, {
        preserveScroll: true,
        onSuccess: () => {
            deleteDialogOpen.value = false;
            deleteTarget.value = null;
        },
    });
};

const toggleRole = (role: string) => {
    userForm.roles = userForm.roles.includes(role)
        ? userForm.roles.filter((item) => item !== role)
        : [...userForm.roles, role];
};

const togglePermission = (permission: string) => {
    roleForm.permissions = roleForm.permissions.includes(permission)
        ? roleForm.permissions.filter((item) => item !== permission)
        : [...roleForm.permissions, permission];
};

const copyCredentials = async () => {
    if (!credentials.value) {
        return;
    }

    const text = `PeopleOps login\nName: ${credentials.value.name}\nEmail: ${credentials.value.email}\nTemporary password: ${credentials.value.password}`;

    try {
        await navigator.clipboard.writeText(text);
        toast.success('Login details copied.');
    } catch {
        toast.error('Copy failed. Select and copy the credentials manually.');
    }
};

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Users & Access',
                href: '/admin/users',
            },
        ],
    },
});
</script>

<template>
    <Head title="Users & Access" />

    <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4 sm:p-6">
        <section
            class="flex flex-col gap-4 rounded-lg border bg-card p-5 text-card-foreground shadow-sm sm:flex-row sm:items-center sm:justify-between"
        >
            <div class="min-w-0">
                <Heading
                    title="Users & Access"
                    description="Manage company users, login access, roles, and permissions from one protected admin workspace."
                    variant="small"
                />
                <div class="mt-3 flex flex-wrap gap-2">
                    <Badge variant="secondary"
                        >{{ stats.users }} total users</Badge
                    >
                    <Badge variant="outline"
                        >{{ activeUserPercentage }}% active</Badge
                    >
                    <Badge variant="outline"
                        >{{ stats.roles }} roles ·
                        {{ stats.permissions }} permissions</Badge
                    >
                </div>
            </div>

            <div
                class="flex items-center gap-2 rounded-md border bg-muted/60 px-3 py-2 text-sm text-muted-foreground dark:bg-muted/40"
            >
                <ShieldCheck class="size-4 text-primary" aria-hidden="true" />
                Admin only
            </div>
        </section>

        <section
            class="inline-flex w-fit gap-1 rounded-lg border bg-muted p-1 text-sm text-muted-foreground"
        >
            <button
                type="button"
                class="inline-flex items-center gap-2 rounded-md px-3 py-2 transition-all duration-200 hover:text-foreground"
                :class="
                    activeTab === 'users'
                        ? 'bg-background text-foreground shadow-xs'
                        : ''
                "
                @click="setTab('users')"
            >
                <UsersRound class="size-4" aria-hidden="true" />
                Users
            </button>
            <button
                type="button"
                class="inline-flex items-center gap-2 rounded-md px-3 py-2 transition-all duration-200 hover:text-foreground"
                :class="
                    activeTab === 'rbac'
                        ? 'bg-background text-foreground shadow-xs'
                        : ''
                "
                @click="setTab('rbac')"
            >
                <LockKeyhole class="size-4" aria-hidden="true" />
                Roles & permissions
            </button>
        </section>

        <Transition name="fade-slide" mode="out-in">
            <section v-if="activeTab === 'users'" key="users" class="space-y-4">
                <Card>
                    <CardHeader
                        class="gap-4 lg:flex-row lg:items-center lg:justify-between"
                    >
                        <div>
                            <CardTitle>User accounts</CardTitle>
                            <CardDescription>
                                Create logins, manage access, and keep NIC/email
                                identity records unique.
                            </CardDescription>
                        </div>
                        <Button @click="openCreateUser">
                            <Plus class="size-4" aria-hidden="true" />
                            Add user
                        </Button>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <form
                            class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_180px_180px_auto_auto]"
                            @submit.prevent="applyUserFilters"
                        >
                            <div class="relative">
                                <Search
                                    class="absolute top-2.5 left-3 size-4 text-muted-foreground"
                                    aria-hidden="true"
                                />
                                <Input
                                    v-model="userFilters.user_search"
                                    class="pl-9"
                                    placeholder="Search name, email, NIC, title"
                                />
                            </div>
                            <select
                                v-model="userFilters.status"
                                class="h-9 rounded-md border bg-background px-3 text-sm shadow-xs transition-colors outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50"
                            >
                                <option value="">All statuses</option>
                                <option value="active">Active</option>
                                <option value="suspended">Suspended</option>
                            </select>
                            <select
                                v-model="userFilters.role"
                                class="h-9 rounded-md border bg-background px-3 text-sm shadow-xs transition-colors outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50"
                            >
                                <option value="">All roles</option>
                                <option
                                    v-for="role in assignableRoles"
                                    :key="role"
                                    :value="role"
                                >
                                    {{ role }}
                                </option>
                            </select>
                            <Button type="submit" variant="outline">
                                <SlidersHorizontal
                                    class="size-4"
                                    aria-hidden="true"
                                />
                                Filter
                            </Button>
                            <Button
                                type="button"
                                variant="ghost"
                                @click="resetUserFilters"
                            >
                                Reset
                            </Button>
                        </form>

                        <div class="overflow-hidden rounded-lg border">
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead
                                        class="border-b bg-muted/60 text-left text-muted-foreground"
                                    >
                                        <tr>
                                            <th class="px-4 py-3 font-medium">
                                                User
                                            </th>
                                            <th class="px-4 py-3 font-medium">
                                                Identity
                                            </th>
                                            <th class="px-4 py-3 font-medium">
                                                Access
                                            </th>
                                            <th class="px-4 py-3 font-medium">
                                                Status
                                            </th>
                                            <th class="px-4 py-3 font-medium">
                                                Last login
                                            </th>
                                            <th
                                                class="px-4 py-3 text-right font-medium"
                                            >
                                                Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="user in users.data"
                                            :key="user.id"
                                            class="border-b transition-colors hover:bg-muted/40"
                                        >
                                            <td class="px-4 py-3">
                                                <div
                                                    class="flex min-w-56 items-center gap-3"
                                                >
                                                    <div
                                                        class="flex size-9 shrink-0 items-center justify-center rounded-md bg-primary/10 text-primary"
                                                    >
                                                        <UserRound
                                                            class="size-4"
                                                            aria-hidden="true"
                                                        />
                                                    </div>
                                                    <div class="min-w-0">
                                                        <p
                                                            class="truncate font-medium text-foreground"
                                                        >
                                                            {{ user.name }}
                                                        </p>
                                                        <p
                                                            class="truncate text-xs text-muted-foreground"
                                                        >
                                                            {{ user.email }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div
                                                    class="min-w-44 text-muted-foreground"
                                                >
                                                    <p class="text-foreground">
                                                        {{ user.nic }}
                                                    </p>
                                                    <p class="text-xs">
                                                        {{
                                                            user.phone ||
                                                            'No phone'
                                                        }}
                                                    </p>
                                                    <p class="text-xs">
                                                        {{
                                                            user.job_title ||
                                                            'No title'
                                                        }}
                                                    </p>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div
                                                    class="flex flex-wrap gap-1"
                                                >
                                                    <Badge
                                                        v-for="role in user.roles"
                                                        :key="role"
                                                        variant="secondary"
                                                    >
                                                        {{ role }}
                                                    </Badge>
                                                    <span
                                                        v-if="
                                                            user.roles
                                                                .length === 0
                                                        "
                                                        class="text-xs text-muted-foreground"
                                                    >
                                                        No role
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <Badge
                                                    :variant="
                                                        user.status === 'active'
                                                            ? 'secondary'
                                                            : 'outline'
                                                    "
                                                >
                                                    {{ user.status }}
                                                </Badge>
                                            </td>
                                            <td
                                                class="min-w-40 px-4 py-3 text-muted-foreground"
                                            >
                                                {{
                                                    formatDate(
                                                        user.last_login_at,
                                                    )
                                                }}
                                            </td>
                                            <td class="px-4 py-3">
                                                <div
                                                    class="flex justify-end gap-2"
                                                >
                                                    <Button
                                                        size="icon-sm"
                                                        variant="ghost"
                                                        :disabled="
                                                            !user.can.update
                                                        "
                                                        @click="
                                                            openEditUser(user)
                                                        "
                                                    >
                                                        <Pencil
                                                            class="size-4"
                                                            aria-hidden="true"
                                                        />
                                                        <span class="sr-only"
                                                            >Edit user</span
                                                        >
                                                    </Button>
                                                    <Button
                                                        size="icon-sm"
                                                        variant="ghost"
                                                        class="text-destructive hover:text-destructive"
                                                        :disabled="
                                                            !user.can.delete
                                                        "
                                                        @click="
                                                            confirmDelete(
                                                                'user',
                                                                user.id,
                                                                user.name,
                                                            )
                                                        "
                                                    >
                                                        <Trash2
                                                            class="size-4"
                                                            aria-hidden="true"
                                                        />
                                                        <span class="sr-only"
                                                            >Delete user</span
                                                        >
                                                    </Button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr v-if="users.data.length === 0">
                                            <td
                                                colspan="6"
                                                class="px-4 py-12 text-center text-muted-foreground"
                                            >
                                                No users match the current
                                                filters.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <AccessPagination
                                :meta="users.meta"
                                :links="users.links"
                            />
                        </div>
                    </CardContent>
                </Card>
            </section>

            <section v-else key="rbac" class="grid gap-4 xl:grid-cols-2">
                <Card>
                    <CardHeader
                        class="gap-4 lg:flex-row lg:items-center lg:justify-between"
                    >
                        <div>
                            <CardTitle>Roles</CardTitle>
                            <CardDescription>
                                Group permissions into assignable access levels.
                            </CardDescription>
                        </div>
                        <Button @click="openCreateRole">
                            <Plus class="size-4" aria-hidden="true" />
                            Create role
                        </Button>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <form
                            class="grid gap-3 sm:grid-cols-[minmax(0,1fr)_auto_auto]"
                            @submit.prevent="applyRoleFilters"
                        >
                            <div class="relative">
                                <Search
                                    class="absolute top-2.5 left-3 size-4 text-muted-foreground"
                                    aria-hidden="true"
                                />
                                <Input
                                    v-model="roleFilters.role_search"
                                    class="pl-9"
                                    placeholder="Search roles"
                                />
                            </div>
                            <Button type="submit" variant="outline">
                                Filter
                            </Button>
                            <Button
                                type="button"
                                variant="ghost"
                                @click="resetRoleFilters"
                            >
                                Reset
                            </Button>
                        </form>

                        <div class="overflow-hidden rounded-lg border">
                            <div
                                v-for="role in roles.data"
                                :key="role.id"
                                class="border-b p-4 transition-colors last:border-b-0 hover:bg-muted/40"
                            >
                                <div
                                    class="flex items-start justify-between gap-3"
                                >
                                    <div class="min-w-0">
                                        <div
                                            class="flex flex-wrap items-center gap-2"
                                        >
                                            <h3
                                                class="truncate font-medium text-foreground"
                                            >
                                                {{ role.name }}
                                            </h3>
                                            <Badge
                                                v-if="role.protected"
                                                variant="outline"
                                                >Protected</Badge
                                            >
                                            <Badge variant="secondary">
                                                {{ role.users_count }} users
                                            </Badge>
                                        </div>
                                        <p
                                            class="mt-1 text-xs text-muted-foreground"
                                        >
                                            {{ role.permissions.length }}
                                            permissions assigned
                                        </p>
                                    </div>
                                    <div class="flex shrink-0 gap-2">
                                        <Button
                                            size="icon-sm"
                                            variant="ghost"
                                            :disabled="!role.can.update"
                                            @click="openEditRole(role)"
                                        >
                                            <Pencil
                                                class="size-4"
                                                aria-hidden="true"
                                            />
                                            <span class="sr-only"
                                                >Edit role</span
                                            >
                                        </Button>
                                        <Button
                                            size="icon-sm"
                                            variant="ghost"
                                            class="text-destructive hover:text-destructive"
                                            :disabled="!role.can.delete"
                                            @click="
                                                confirmDelete(
                                                    'role',
                                                    role.id,
                                                    role.name,
                                                )
                                            "
                                        >
                                            <Trash2
                                                class="size-4"
                                                aria-hidden="true"
                                            />
                                            <span class="sr-only"
                                                >Delete role</span
                                            >
                                        </Button>
                                    </div>
                                </div>
                                <div class="mt-3 flex flex-wrap gap-1">
                                    <Badge
                                        v-for="permission in role.permissions.slice(
                                            0,
                                            8,
                                        )"
                                        :key="permission"
                                        variant="outline"
                                    >
                                        {{ permission }}
                                    </Badge>
                                    <Badge
                                        v-if="role.permissions.length > 8"
                                        variant="secondary"
                                    >
                                        +{{ role.permissions.length - 8 }}
                                    </Badge>
                                </div>
                            </div>
                            <div
                                v-if="roles.data.length === 0"
                                class="p-12 text-center text-sm text-muted-foreground"
                            >
                                No roles match the current filters.
                            </div>
                            <AccessPagination
                                :meta="roles.meta"
                                :links="roles.links"
                            />
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader
                        class="gap-4 lg:flex-row lg:items-center lg:justify-between"
                    >
                        <div>
                            <CardTitle>Permissions</CardTitle>
                            <CardDescription>
                                Manage permission keys used by policy and menu
                                visibility decisions.
                            </CardDescription>
                        </div>
                        <Button @click="openCreatePermission">
                            <Plus class="size-4" aria-hidden="true" />
                            Create permission
                        </Button>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <form
                            class="grid gap-3 sm:grid-cols-[minmax(0,1fr)_auto_auto]"
                            @submit.prevent="applyRoleFilters"
                        >
                            <div class="relative">
                                <Search
                                    class="absolute top-2.5 left-3 size-4 text-muted-foreground"
                                    aria-hidden="true"
                                />
                                <Input
                                    v-model="roleFilters.permission_search"
                                    class="pl-9"
                                    placeholder="Search permissions"
                                />
                            </div>
                            <Button type="submit" variant="outline">
                                Filter
                            </Button>
                            <Button
                                type="button"
                                variant="ghost"
                                @click="resetRoleFilters"
                            >
                                Reset
                            </Button>
                        </form>

                        <div class="overflow-hidden rounded-lg border">
                            <div
                                v-for="permission in permissions.data"
                                :key="permission.id"
                                class="flex items-center justify-between gap-3 border-b p-4 transition-colors last:border-b-0 hover:bg-muted/40"
                            >
                                <div class="min-w-0">
                                    <div class="flex flex-wrap gap-2">
                                        <p
                                            class="truncate font-medium text-foreground"
                                        >
                                            {{ permission.name }}
                                        </p>
                                        <Badge
                                            v-if="permission.protected"
                                            variant="outline"
                                            >System</Badge
                                        >
                                    </div>
                                    <p
                                        class="mt-1 text-xs text-muted-foreground"
                                    >
                                        Assigned to
                                        {{ permission.roles_count }} roles
                                    </p>
                                </div>
                                <div class="flex shrink-0 gap-2">
                                    <Button
                                        size="icon-sm"
                                        variant="ghost"
                                        :disabled="!permission.can.update"
                                        @click="openEditPermission(permission)"
                                    >
                                        <Pencil
                                            class="size-4"
                                            aria-hidden="true"
                                        />
                                        <span class="sr-only"
                                            >Edit permission</span
                                        >
                                    </Button>
                                    <Button
                                        size="icon-sm"
                                        variant="ghost"
                                        class="text-destructive hover:text-destructive"
                                        :disabled="!permission.can.delete"
                                        @click="
                                            confirmDelete(
                                                'permission',
                                                permission.id,
                                                permission.name,
                                            )
                                        "
                                    >
                                        <Trash2
                                            class="size-4"
                                            aria-hidden="true"
                                        />
                                        <span class="sr-only"
                                            >Delete permission</span
                                        >
                                    </Button>
                                </div>
                            </div>
                            <div
                                v-if="permissions.data.length === 0"
                                class="p-12 text-center text-sm text-muted-foreground"
                            >
                                No permissions match the current filters.
                            </div>
                            <AccessPagination
                                :meta="permissions.meta"
                                :links="permissions.links"
                            />
                        </div>
                    </CardContent>
                </Card>
            </section>
        </Transition>

        <Dialog v-model:open="userDialogOpen">
            <DialogContent class="max-h-[90vh] overflow-y-auto sm:max-w-2xl">
                <DialogHeader>
                    <DialogTitle>{{ userDialogTitle }}</DialogTitle>
                    <DialogDescription>
                        Admin role assignment is protected. New users receive a
                        temporary password you can copy after creation.
                    </DialogDescription>
                </DialogHeader>
                <form class="space-y-5" @submit.prevent="submitUser">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="space-y-2">
                            <Label for="user-name">Full name</Label>
                            <Input
                                id="user-name"
                                v-model="userForm.name"
                                autocomplete="name"
                                placeholder="Jane Perera"
                            />
                            <InputError :message="userForm.errors.name" />
                        </div>
                        <div class="space-y-2">
                            <Label for="user-email">Email</Label>
                            <Input
                                id="user-email"
                                v-model="userForm.email"
                                autocomplete="email"
                                placeholder="jane@example.com"
                            />
                            <InputError :message="userForm.errors.email" />
                        </div>
                        <div class="space-y-2">
                            <Label for="user-nic">NIC</Label>
                            <Input
                                id="user-nic"
                                v-model="userForm.nic"
                                placeholder="199012345678"
                            />
                            <InputError :message="userForm.errors.nic" />
                        </div>
                        <div class="space-y-2">
                            <Label for="user-phone">Phone</Label>
                            <Input
                                id="user-phone"
                                v-model="userForm.phone"
                                autocomplete="tel"
                                placeholder="+94 77 123 4567"
                            />
                            <InputError :message="userForm.errors.phone" />
                        </div>
                        <div class="space-y-2">
                            <Label for="user-title">Job title</Label>
                            <Input
                                id="user-title"
                                v-model="userForm.job_title"
                                placeholder="HR Executive"
                            />
                            <InputError :message="userForm.errors.job_title" />
                        </div>
                        <div class="space-y-2">
                            <Label for="user-status">Status</Label>
                            <select
                                id="user-status"
                                v-model="userForm.status"
                                class="h-9 w-full rounded-md border bg-background px-3 text-sm shadow-xs transition-colors outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50"
                            >
                                <option value="active">Active</option>
                                <option value="suspended">Suspended</option>
                            </select>
                            <InputError :message="userForm.errors.status" />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div class="flex items-center justify-between gap-3">
                            <Label for="user-password">
                                {{
                                    editingUser
                                        ? 'New password'
                                        : 'Temporary password'
                                }}
                            </Label>
                            <Button
                                type="button"
                                variant="ghost"
                                size="sm"
                                @click="userForm.password = generatePassword()"
                            >
                                <KeyRound class="size-4" aria-hidden="true" />
                                Generate
                            </Button>
                        </div>
                        <Input
                            id="user-password"
                            v-model="userForm.password"
                            autocomplete="new-password"
                            :placeholder="
                                editingUser
                                    ? 'Leave blank to keep current password'
                                    : 'Temporary password'
                            "
                        />
                        <InputError :message="userForm.errors.password" />
                    </div>

                    <div class="space-y-2">
                        <Label>Assignable roles</Label>
                        <div class="flex flex-wrap gap-2">
                            <button
                                v-for="role in assignableRoles"
                                :key="role"
                                type="button"
                                class="inline-flex items-center gap-2 rounded-md border px-3 py-2 text-sm transition-all duration-200 hover:-translate-y-0.5 hover:bg-accent hover:text-accent-foreground"
                                :class="
                                    userForm.roles.includes(role)
                                        ? 'border-primary bg-primary/10 text-primary'
                                        : 'bg-background text-muted-foreground'
                                "
                                @click="toggleRole(role)"
                            >
                                <Check
                                    v-if="userForm.roles.includes(role)"
                                    class="size-4"
                                    aria-hidden="true"
                                />
                                {{ role }}
                            </button>
                        </div>
                        <InputError :message="userForm.errors.roles" />
                        <InputError :message="userForm.errors['roles.0']" />
                    </div>

                    <DialogFooter>
                        <Button
                            type="button"
                            variant="ghost"
                            @click="userDialogOpen = false"
                        >
                            Cancel
                        </Button>
                        <Button type="submit" :disabled="userForm.processing">
                            {{
                                userForm.processing
                                    ? 'Saving...'
                                    : editingUser
                                      ? 'Save changes'
                                      : 'Create login'
                            }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <Dialog v-model:open="roleDialogOpen">
            <DialogContent class="max-h-[90vh] overflow-y-auto sm:max-w-2xl">
                <DialogHeader>
                    <DialogTitle>{{ roleDialogTitle }}</DialogTitle>
                    <DialogDescription>
                        Use lowercase slugs such as hr-manager. Assign only the
                        permissions this role needs.
                    </DialogDescription>
                </DialogHeader>
                <form class="space-y-5" @submit.prevent="submitRole">
                    <div class="space-y-2">
                        <Label for="role-name">Role name</Label>
                        <Input
                            id="role-name"
                            v-model="roleForm.name"
                            placeholder="hr-manager"
                        />
                        <InputError :message="roleForm.errors.name" />
                    </div>
                    <div class="space-y-2">
                        <Label>Permissions</Label>
                        <div class="grid gap-2 sm:grid-cols-2">
                            <button
                                v-for="permission in allPermissions"
                                :key="permission"
                                type="button"
                                class="flex items-center justify-between gap-2 rounded-md border px-3 py-2 text-left text-sm transition-all duration-200 hover:-translate-y-0.5 hover:bg-accent hover:text-accent-foreground"
                                :class="
                                    roleForm.permissions.includes(permission)
                                        ? 'border-primary bg-primary/10 text-primary'
                                        : 'bg-background text-muted-foreground'
                                "
                                @click="togglePermission(permission)"
                            >
                                <span class="min-w-0 truncate">{{
                                    permission
                                }}</span>
                                <Check
                                    v-if="
                                        roleForm.permissions.includes(
                                            permission,
                                        )
                                    "
                                    class="size-4 shrink-0"
                                    aria-hidden="true"
                                />
                            </button>
                        </div>
                        <InputError :message="roleForm.errors.permissions" />
                    </div>
                    <DialogFooter>
                        <Button
                            type="button"
                            variant="ghost"
                            @click="roleDialogOpen = false"
                        >
                            Cancel
                        </Button>
                        <Button type="submit" :disabled="roleForm.processing">
                            {{
                                roleForm.processing
                                    ? 'Saving...'
                                    : editingRole
                                      ? 'Save role'
                                      : 'Create role'
                            }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <Dialog v-model:open="permissionDialogOpen">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>{{ permissionDialogTitle }}</DialogTitle>
                    <DialogDescription>
                        Permission keys should describe the action, for example
                        employees.view or leave.approve.
                    </DialogDescription>
                </DialogHeader>
                <form class="space-y-5" @submit.prevent="submitPermission">
                    <div class="space-y-2">
                        <Label for="permission-name">Permission name</Label>
                        <Input
                            id="permission-name"
                            v-model="permissionForm.name"
                            placeholder="employees.view"
                        />
                        <InputError :message="permissionForm.errors.name" />
                    </div>
                    <DialogFooter>
                        <Button
                            type="button"
                            variant="ghost"
                            @click="permissionDialogOpen = false"
                        >
                            Cancel
                        </Button>
                        <Button
                            type="submit"
                            :disabled="permissionForm.processing"
                        >
                            {{
                                permissionForm.processing
                                    ? 'Saving...'
                                    : editingPermission
                                      ? 'Save permission'
                                      : 'Create permission'
                            }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <Dialog v-model:open="deleteDialogOpen">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Confirm delete</DialogTitle>
                    <DialogDescription>
                        This action is restricted and audited. You are deleting
                        <strong>{{ deleteTarget?.label }}</strong
                        >.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button
                        type="button"
                        variant="ghost"
                        @click="deleteDialogOpen = false"
                    >
                        Cancel
                    </Button>
                    <Button
                        type="button"
                        variant="destructive"
                        :disabled="deleteForm.processing"
                        @click="submitDelete"
                    >
                        {{ deleteForm.processing ? 'Deleting...' : 'Delete' }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <Dialog v-model:open="credentialsDialogOpen">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Login created</DialogTitle>
                    <DialogDescription>
                        Share these details securely. The temporary password is
                        shown only once in this confirmation.
                    </DialogDescription>
                </DialogHeader>
                <div
                    v-if="credentials"
                    class="rounded-lg border bg-muted/50 p-4 font-mono text-sm"
                >
                    <p class="font-sans font-medium text-foreground">
                        {{ credentials.name }}
                    </p>
                    <p class="mt-3 text-muted-foreground">Email</p>
                    <p>{{ credentials.email }}</p>
                    <p class="mt-3 text-muted-foreground">Temporary password</p>
                    <p>{{ credentials.password }}</p>
                </div>
                <DialogFooter>
                    <Button
                        type="button"
                        variant="ghost"
                        @click="credentialsDialogOpen = false"
                    >
                        Close
                    </Button>
                    <Button type="button" @click="copyCredentials">
                        <Clipboard class="size-4" aria-hidden="true" />
                        Copy login
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>

<style scoped>
.fade-slide-enter-active,
.fade-slide-leave-active {
    transition:
        opacity 160ms ease,
        transform 160ms ease;
}

.fade-slide-enter-from,
.fade-slide-leave-to {
    opacity: 0;
    transform: translateY(4px);
}
</style>
