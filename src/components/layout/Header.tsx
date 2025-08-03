import React, { useState } from 'react';
import { useAuth } from '@/hooks/useAuth';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card } from '@/components/ui/card';
import { Bell, Search, Settings, ChevronDown } from 'lucide-react';
import { Notification } from '@/types';

export const Header: React.FC = () => {
  const { user } = useAuth();
  const [notifications] = useState<Notification[]>([
    {
      id: '1',
      user_id: user?.id || '',
      title: 'Nova entrevista pendente',
      message: 'João Silva enviou respostas para aprovação',
      type: 'info',
      read: false,
      created_at: new Date().toISOString()
    },
    {
      id: '2',
      user_id: user?.id || '',
      title: 'Upload aprovado',
      message: 'Fotos da sessão foram aprovadas',
      type: 'success',
      read: false,
      created_at: new Date().toISOString()
    }
  ]);

  const unreadCount = notifications.filter(n => !n.read).length;

  return (
    <header className="h-16 bg-card border-b border-border px-6 flex items-center justify-between">
      {/* Search */}
      <div className="flex-1 max-w-md">
        <div className="relative">
          <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-muted-foreground" />
          <input
            type="text"
            placeholder="Buscar..."
            className="w-full pl-10 pr-4 py-2 border border-input rounded-md bg-background focus:ring-2 focus:ring-ring focus:border-transparent"
          />
        </div>
      </div>

      {/* Right side */}
      <div className="flex items-center space-x-4">
        {/* Notifications */}
        <div className="relative">
          <Button variant="ghost" size="icon">
            <Bell className="w-5 h-5" />
            {unreadCount > 0 && (
              <Badge 
                variant="destructive" 
                className="absolute -top-1 -right-1 h-5 w-5 text-xs p-0 flex items-center justify-center"
              >
                {unreadCount}
              </Badge>
            )}
          </Button>
        </div>

        {/* Settings */}
        <Button variant="ghost" size="icon">
          <Settings className="w-5 h-5" />
        </Button>

        {/* User Menu */}
        <div className="flex items-center space-x-3">
          <div className="text-right">
            <p className="text-sm font-medium">{user?.name}</p>
            <p className="text-xs text-muted-foreground">
              {user?.role === 'admin' ? 'Administrador' : 
               user?.role === 'collaborator' ? 'Colaborador' :
               user?.role === 'athlete' ? 'Atleta' : 'Editora Parceira'}
            </p>
          </div>
          <div className="w-8 h-8 bg-primary rounded-full flex items-center justify-center">
            <span className="text-primary-foreground text-sm font-medium">
              {user?.name?.charAt(0) || 'U'}
            </span>
          </div>
          <ChevronDown className="w-4 h-4 text-muted-foreground" />
        </div>
      </div>
    </header>
  );
};