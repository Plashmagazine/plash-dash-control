import React from 'react';
import { AppLayout } from '@/components/layout/AppLayout';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Progress } from '@/components/ui/progress';
import {
  Users,
  FileImage,
  BookOpen,
  TrendingUp,
  AlertTriangle,
  CheckCircle,
  Clock,
  Star,
  Activity,
  Upload,
  MessageSquare
} from 'lucide-react';

export const AdminDashboard: React.FC = () => {
  // Mock data para demonstração
  const stats = {
    editions: { total: 12, active: 3, completed: 9 },
    athletes: { total: 45, active: 15, verified: 40 },
    collaborators: { total: 28, active: 12, topRated: 8 },
    partners: { total: 8, active: 6, goldPartners: 3 }
  };

  const recentActivity = [
    {
      id: 1,
      user: 'João Silva',
      action: 'enviou fotos da sessão',
      time: '2 min atrás',
      type: 'upload',
      status: 'pending'
    },
    {
      id: 2,
      user: 'Maria Santos',
      action: 'completou entrevista',
      time: '15 min atrás',
      type: 'interview',
      status: 'approved'
    },
    {
      id: 3,
      user: 'Editora X',
      action: 'enviou revista digital',
      time: '1h atrás',
      type: 'product',
      status: 'review'
    }
  ];

  const pendingTasks = [
    { id: 1, title: 'Aprovar entrevista de João Silva', priority: 'high', type: 'interview' },
    { id: 2, title: 'Revisar uploads da Edição #12', priority: 'medium', type: 'upload' },
    { id: 3, title: 'Configurar nova capa', priority: 'low', type: 'cover' }
  ];

  return (
    <AppLayout>
      <div className="space-y-6">
        {/* Header */}
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-3xl font-bold">Dashboard Administrativo</h1>
            <p className="text-muted-foreground">Visão geral da plataforma Plash Magazine</p>
          </div>
          <Button variant="editorial" className="flex items-center">
            <Activity className="w-4 h-4 mr-2" />
            Sistema Funcionando
          </Button>
        </div>

        {/* Stats Cards */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <Card>
            <CardContent className="p-6">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-muted-foreground">Edições</p>
                  <div className="flex items-baseline space-x-2">
                    <span className="text-2xl font-bold">{stats.editions.total}</span>
                    <Badge variant="success">{stats.editions.active} ativas</Badge>
                  </div>
                  <Progress value={(stats.editions.completed / stats.editions.total) * 100} className="mt-2" />
                </div>
                <BookOpen className="w-8 h-8 text-primary" />
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardContent className="p-6">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-muted-foreground">Atletas</p>
                  <div className="flex items-baseline space-x-2">
                    <span className="text-2xl font-bold">{stats.athletes.total}</span>
                    <Badge variant="verified">{stats.athletes.verified} verificados</Badge>
                  </div>
                  <p className="text-xs text-muted-foreground mt-1">
                    {stats.athletes.active} ativos este mês
                  </p>
                </div>
                <Users className="w-8 h-8 text-primary" />
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardContent className="p-6">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-muted-foreground">Colaboradores</p>
                  <div className="flex items-baseline space-x-2">
                    <span className="text-2xl font-bold">{stats.collaborators.total}</span>
                    <Badge variant="approved">{stats.collaborators.topRated} top rated</Badge>
                  </div>
                  <p className="text-xs text-muted-foreground mt-1">
                    {stats.collaborators.active} ativos
                  </p>
                </div>
                <FileImage className="w-8 h-8 text-primary" />
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardContent className="p-6">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-muted-foreground">Editoras</p>
                  <div className="flex items-baseline space-x-2">
                    <span className="text-2xl font-bold">{stats.partners.total}</span>
                    <Badge variant="warning">{stats.partners.goldPartners} gold</Badge>
                  </div>
                  <p className="text-xs text-muted-foreground mt-1">
                    {stats.partners.active} ativas
                  </p>
                </div>
                <TrendingUp className="w-8 h-8 text-primary" />
              </div>
            </CardContent>
          </Card>
        </div>

        {/* Main Content Grid */}
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
          {/* Pending Tasks */}
          <Card className="lg:col-span-2">
            <CardHeader>
              <CardTitle className="flex items-center">
                <AlertTriangle className="w-5 h-5 mr-2 text-warning" />
                Tarefas Pendentes
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div className="space-y-4">
                {pendingTasks.map((task) => (
                  <div key={task.id} className="flex items-center justify-between p-4 border rounded-lg">
                    <div className="flex items-center space-x-3">
                      <div className="w-2 h-2 rounded-full bg-warning"></div>
                      <div>
                        <p className="font-medium">{task.title}</p>
                        <div className="flex items-center space-x-2 mt-1">
                          <Badge variant={task.priority === 'high' ? 'destructive' : task.priority === 'medium' ? 'warning' : 'secondary'}>
                            {task.priority === 'high' ? 'Alta' : task.priority === 'medium' ? 'Média' : 'Baixa'}
                          </Badge>
                          <span className="text-xs text-muted-foreground">{task.type}</span>
                        </div>
                      </div>
                    </div>
                    <Button size="sm">Revisar</Button>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>

          {/* Recent Activity */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center">
                <Clock className="w-5 h-5 mr-2 text-info" />
                Atividade Recente
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div className="space-y-4">
                {recentActivity.map((activity) => (
                  <div key={activity.id} className="flex items-start space-x-3">
                    <div className="flex-shrink-0">
                      {activity.type === 'upload' && <Upload className="w-4 h-4 text-info" />}
                      {activity.type === 'interview' && <MessageSquare className="w-4 h-4 text-success" />}
                      {activity.type === 'product' && <FileImage className="w-4 h-4 text-warning" />}
                    </div>
                    <div className="flex-1 min-w-0">
                      <p className="text-sm">
                        <span className="font-medium">{activity.user}</span>{' '}
                        {activity.action}
                      </p>
                      <div className="flex items-center space-x-2 mt-1">
                        <span className="text-xs text-muted-foreground">{activity.time}</span>
                        <Badge 
                          variant={
                            activity.status === 'approved' ? 'success' :
                            activity.status === 'pending' ? 'warning' : 'info'
                          }
                          className="text-xs"
                        >
                          {activity.status === 'approved' ? 'Aprovado' :
                           activity.status === 'pending' ? 'Pendente' : 'Em análise'}
                        </Badge>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>
        </div>

        {/* Quick Actions */}
        <Card>
          <CardHeader>
            <CardTitle>Ações Rápidas</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
              <Button variant="outline" className="h-20 flex-col">
                <BookOpen className="w-6 h-6 mb-2" />
                Nova Edição
              </Button>
              <Button variant="outline" className="h-20 flex-col">
                <FileImage className="w-6 h-6 mb-2" />
                Nova Capa
              </Button>
              <Button variant="outline" className="h-20 flex-col">
                <Users className="w-6 h-6 mb-2" />
                Adicionar Usuário
              </Button>
              <Button variant="outline" className="h-20 flex-col">
                <Activity className="w-6 h-6 mb-2" />
                Diagnóstico
              </Button>
            </div>
          </CardContent>
        </Card>
      </div>
    </AppLayout>
  );
};