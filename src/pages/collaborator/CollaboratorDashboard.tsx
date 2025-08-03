import React from 'react';
import { AppLayout } from '@/components/layout/AppLayout';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Progress } from '@/components/ui/progress';
import {
  Camera,
  Upload,
  FileImage,
  DollarSign,
  Star,
  Clock,
  CheckCircle,
  MessageSquare,
  HeartHandshake
} from 'lucide-react';

export const CollaboratorDashboard: React.FC = () => {
  const stats = {
    projects: { active: 2, completed: 15, total: 17 },
    uploads: { pending: 3, approved: 45, total: 48 },
    commission: { current: 2500, total: 18750 },
    rating: 4.8
  };

  const activeProjects = [
    {
      id: 1,
      edition: 'Edição #12',
      athlete: 'João Silva',
      status: 'Em andamento',
      deadline: '2024-08-15',
      progress: 65
    },
    {
      id: 2,
      edition: 'Edição #13',
      athlete: 'Maria Santos',
      status: 'Aguardando aprovação',
      deadline: '2024-08-20',
      progress: 90
    }
  ];

  return (
    <AppLayout>
      <div className="space-y-6">
        {/* Header */}
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-3xl font-bold">Dashboard do Colaborador</h1>
            <p className="text-muted-foreground">Bem-vindo de volta! Acompanhe seus projetos.</p>
          </div>
          <div className="flex items-center space-x-2">
            <Star className="w-5 h-5 text-warning" />
            <span className="font-bold text-lg">{stats.rating}</span>
            <Badge variant="verified">Verificado</Badge>
          </div>
        </div>

        {/* Stats Cards */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <Card>
            <CardContent className="p-6">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-muted-foreground">Projetos</p>
                  <div className="flex items-baseline space-x-2">
                    <span className="text-2xl font-bold">{stats.projects.active}</span>
                    <Badge variant="info">ativos</Badge>
                  </div>
                  <p className="text-xs text-muted-foreground mt-1">
                    {stats.projects.completed} concluídos
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
                  <p className="text-sm font-medium text-muted-foreground">Uploads</p>
                  <div className="flex items-baseline space-x-2">
                    <span className="text-2xl font-bold">{stats.uploads.total}</span>
                    <Badge variant="success">{stats.uploads.approved} aprovados</Badge>
                  </div>
                  <p className="text-xs text-muted-foreground mt-1">
                    {stats.uploads.pending} pendentes
                  </p>
                </div>
                <Upload className="w-8 h-8 text-primary" />
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardContent className="p-6">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-muted-foreground">Comissão</p>
                  <div className="flex items-baseline space-x-2">
                    <span className="text-2xl font-bold">R$ {stats.commission.current.toLocaleString()}</span>
                  </div>
                  <p className="text-xs text-muted-foreground mt-1">
                    R$ {stats.commission.total.toLocaleString()} total
                  </p>
                </div>
                <DollarSign className="w-8 h-8 text-success" />
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardContent className="p-6">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-muted-foreground">Avaliação</p>
                  <div className="flex items-baseline space-x-2">
                    <span className="text-2xl font-bold">{stats.rating}</span>
                    <div className="flex">
                      {[...Array(5)].map((_, i) => (
                        <Star 
                          key={i} 
                          className={`w-4 h-4 ${i < Math.floor(stats.rating) ? 'text-warning fill-current' : 'text-muted-foreground'}`} 
                        />
                      ))}
                    </div>
                  </div>
                  <p className="text-xs text-muted-foreground mt-1">
                    Baseado em {stats.projects.completed} projetos
                  </p>
                </div>
                <Star className="w-8 h-8 text-warning" />
              </div>
            </CardContent>
          </Card>
        </div>

        {/* Active Projects */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center">
              <Camera className="w-5 h-5 mr-2" />
              Projetos Ativos
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              {activeProjects.map((project) => (
                <div key={project.id} className="border rounded-lg p-4">
                  <div className="flex items-center justify-between mb-3">
                    <div>
                      <h3 className="font-semibold">{project.edition}</h3>
                      <p className="text-sm text-muted-foreground">Atleta: {project.athlete}</p>
                    </div>
                    <div className="text-right">
                      <Badge variant={project.status === 'Em andamento' ? 'info' : 'warning'}>
                        {project.status}
                      </Badge>
                      <p className="text-xs text-muted-foreground mt-1">
                        Prazo: {new Date(project.deadline).toLocaleDateString('pt-BR')}
                      </p>
                    </div>
                  </div>
                  <div className="space-y-2">
                    <div className="flex justify-between text-sm">
                      <span>Progresso</span>
                      <span>{project.progress}%</span>
                    </div>
                    <Progress value={project.progress} />
                  </div>
                  <div className="flex justify-end mt-3">
                    <Button size="sm">Ver Detalhes</Button>
                  </div>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>

        {/* Quick Actions */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
          <Card>
            <CardHeader>
              <CardTitle className="text-lg flex items-center">
                <Upload className="w-5 h-5 mr-2" />
                Upload Rápido
              </CardTitle>
            </CardHeader>
            <CardContent>
              <p className="text-sm text-muted-foreground mb-4">
                Envie suas fotos e vídeos da sessão mais recente.
              </p>
              <Button className="w-full">
                <Upload className="w-4 h-4 mr-2" />
                Fazer Upload
              </Button>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle className="text-lg flex items-center">
                <MessageSquare className="w-5 h-5 mr-2" />
                Entrevistas
              </CardTitle>
            </CardHeader>
            <CardContent>
              <p className="text-sm text-muted-foreground mb-4">
                Responda às entrevistas pendentes dos seus projetos.
              </p>
              <Button variant="outline" className="w-full">
                <MessageSquare className="w-4 h-4 mr-2" />
                Ver Entrevistas
              </Button>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle className="text-lg flex items-center">
                <HeartHandshake className="w-5 h-5 mr-2" />
                Indicar Talento
              </CardTitle>
            </CardHeader>
            <CardContent>
              <p className="text-sm text-muted-foreground mb-4">
                Conhece algum talento? Indique para a Plash Magazine.
              </p>
              <Button variant="success" className="w-full">
                <HeartHandshake className="w-4 h-4 mr-2" />
                Indicar
              </Button>
            </CardContent>
          </Card>
        </div>
      </div>
    </AppLayout>
  );
};