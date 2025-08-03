import React from 'react';
import { Link, useLocation } from 'react-router-dom';
import { useAuth } from '@/hooks/useAuth';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { cn } from '@/lib/utils';
import {
  LayoutDashboard,
  Users,
  FileImage,
  Upload,
  MessageSquare,
  Settings,
  BarChart3,
  Award,
  HeartHandshake,
  Activity,
  LogOut,
  User,
  Camera,
  BookOpen,
  Building,
  TrendingUp,
  Star,
  Target
} from 'lucide-react';

interface SidebarItem {
  title: string;
  href: string;
  icon: React.ComponentType<any>;
  badge?: string;
  role?: string[];
}

export const Sidebar: React.FC = () => {
  const { user, logout } = useAuth();
  const location = useLocation();

  const getSidebarItems = (): SidebarItem[] => {
    if (!user) return [];

    switch (user.role) {
      case 'admin':
        return [
          { title: 'Dashboard', href: '/admin', icon: LayoutDashboard },
          { title: 'Diagnóstico', href: '/admin/diagnostics', icon: Activity, badge: 'Novo' },
          { title: 'Edições', href: '/admin/editions', icon: BookOpen },
          { title: 'Capas', href: '/admin/covers', icon: FileImage },
          { title: 'Participantes', href: '/admin/athletes', icon: User },
          { title: 'Colaboradores', href: '/admin/collaborators', icon: Camera },
          { title: 'Editoras Parceiras', href: '/admin/partners', icon: Building },
          { title: 'Entrevistas', href: '/admin/interviews', icon: MessageSquare },
          { title: 'Uploads', href: '/admin/uploads', icon: Upload },
          { title: 'Indicações', href: '/admin/indications', icon: HeartHandshake },
          { title: 'Relatórios', href: '/admin/reports', icon: BarChart3 },
          { title: 'Configurações', href: '/admin/settings', icon: Settings },
        ];

      case 'collaborator':
        return [
          { title: 'Dashboard', href: '/collaborator', icon: LayoutDashboard },
          { title: 'Meus Projetos', href: '/collaborator/projects', icon: FileImage },
          { title: 'Uploads', href: '/collaborator/uploads', icon: Upload },
          { title: 'Entrevistas', href: '/collaborator/interviews', icon: MessageSquare },
          { title: 'Contratos', href: '/collaborator/contracts', icon: FileImage },
          { title: 'Indicar Talentos', href: '/collaborator/indications', icon: HeartHandshake },
          { title: 'Meu Perfil', href: '/collaborator/profile', icon: User },
          { title: 'Histórico', href: '/collaborator/history', icon: BarChart3 },
        ];

      case 'athlete':
        return [
          { title: 'Dashboard', href: '/athlete', icon: LayoutDashboard },
          { title: 'Minha Capa', href: '/athlete/cover', icon: FileImage },
          { title: 'Entrevista', href: '/athlete/interview', icon: MessageSquare },
          { title: 'Uploads', href: '/athlete/uploads', icon: Upload },
          { title: 'Meu Perfil', href: '/athlete/profile', icon: User },
        ];

      case 'partner':
        return [
          { title: 'Dashboard', href: '/partner', icon: LayoutDashboard },
          { title: 'Minhas Edições', href: '/partner/editions', icon: BookOpen },
          { title: 'Produto Digital', href: '/partner/digital', icon: Upload },
          { title: 'Produto Impresso', href: '/partner/printed', icon: FileImage },
          { title: 'Relatórios', href: '/partner/reports', icon: BarChart3 },
          { title: 'Ranking', href: '/partner/ranking', icon: Award },
          { title: 'Meu Perfil', href: '/partner/profile', icon: Building },
          { title: 'Indicar Talentos', href: '/partner/indications', icon: HeartHandshake },
        ];

      default:
        return [];
    }
  };

  const sidebarItems = getSidebarItems();

  const getUserRoleDisplay = () => {
    switch (user?.role) {
      case 'admin':
        return user.sub_role === 'ceo' ? 'CEO' : user.sub_role === 'socio' ? 'Sócio' : 'Administrador';
      case 'collaborator':
        return 'Colaborador';
      case 'athlete':
        return 'Atleta';
      case 'partner':
        return 'Editora Parceira';
      default:
        return 'Usuário';
    }
  };

  return (
    <div className="flex flex-col h-full bg-sidebar border-r border-sidebar-border">
      {/* Header */}
      <div className="p-6 border-b border-sidebar-border">
        <div className="flex items-center space-x-3">
          <div className="w-10 h-10 bg-gradient-editorial rounded-lg flex items-center justify-center">
            <span className="text-primary-foreground font-bold text-lg">P</span>
          </div>
          <div>
            <h2 className="font-bold text-sidebar-foreground">Plash Magazine</h2>
            <p className="text-sm text-sidebar-foreground/70">Plataforma Editorial</p>
          </div>
        </div>
      </div>

      {/* User Info */}
      <div className="p-4 border-b border-sidebar-border">
        <div className="flex items-center space-x-3">
          <div className="w-8 h-8 bg-sidebar-accent rounded-full flex items-center justify-center">
            <User className="w-4 h-4 text-sidebar-accent-foreground" />
          </div>
          <div className="flex-1 min-w-0">
            <p className="text-sm font-medium text-sidebar-foreground truncate">
              {user?.name}
            </p>
            <div className="flex items-center space-x-2">
              <Badge variant="verified" className="text-xs">
                {getUserRoleDisplay()}
              </Badge>
              {user?.badges.includes('verificado') && (
                <Star className="w-3 h-3 text-badge-verified" />
              )}
            </div>
          </div>
        </div>
      </div>

      {/* Navigation */}
      <nav className="flex-1 p-4 space-y-1 overflow-y-auto">
        {sidebarItems.map((item) => {
          const isActive = location.pathname === item.href;
          const Icon = item.icon;
          
          return (
            <Link key={item.href} to={item.href}>
              <Button
                variant={isActive ? 'secondary' : 'ghost'}
                className={cn(
                  'w-full justify-start h-10',
                  isActive && 'bg-sidebar-accent text-sidebar-accent-foreground',
                  !isActive && 'text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground'
                )}
              >
                <Icon className="w-4 h-4 mr-3" />
                <span className="flex-1 text-left">{item.title}</span>
                {item.badge && (
                  <Badge variant="info" className="ml-2 text-xs">
                    {item.badge}
                  </Badge>
                )}
              </Button>
            </Link>
          );
        })}
      </nav>

      {/* Footer */}
      <div className="p-4 border-t border-sidebar-border">
        <Button
          variant="ghost"
          className="w-full justify-start text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground"
          onClick={logout}
        >
          <LogOut className="w-4 h-4 mr-3" />
          Sair
        </Button>
      </div>
    </div>
  );
};