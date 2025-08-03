import React, { useState, useEffect } from 'react';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Progress } from '@/components/ui/progress';
import { 
  CheckCircle, 
  XCircle, 
  AlertTriangle, 
  RefreshCw, 
  Database, 
  Server, 
  FolderOpen, 
  Route,
  FileText,
  Settings,
  Download,
  Trash2,
  Shield
} from 'lucide-react';
import { SystemStatus, DirectoryStatus, RouteStatus, LogEntry } from '@/types';

export const SystemDiagnostics: React.FC = () => {
  const [systemStatus, setSystemStatus] = useState<SystemStatus | null>(null);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [logs, setLogs] = useState<LogEntry[]>([]);
  const [logFilter, setLogFilter] = useState<'all' | 'error' | 'warning' | 'info'>('all');

  // Simulação de dados para desenvolvimento
  const generateMockSystemStatus = (): SystemStatus => {
    return {
      database: {
        status: 'online',
        tables_count: 15,
        missing_tables: []
      },
      php: {
        version: '8.2.0',
        memory_limit: '256M',
        max_execution_time: '300',
        extensions: ['pdo', 'mbstring', 'curl', 'gd', 'zip'],
        missing_extensions: []
      },
      server: {
        software: 'Apache/2.4.41',
        document_root: '/var/www/html',
        disk_space: {
          total: '50GB',
          used: '15GB',
          free: '35GB'
        }
      },
      directories: [
        { path: '/uploads', exists: true, readable: true, writable: true, permissions: '755' },
        { path: '/logs', exists: true, readable: true, writable: true, permissions: '755' },
        { path: '/config', exists: true, readable: true, writable: false, permissions: '644' },
        { path: '/admin', exists: true, readable: true, writable: false, permissions: '644' },
        { path: '/athlete', exists: true, readable: true, writable: false, permissions: '644' },
        { path: '/collaborator', exists: true, readable: true, writable: false, permissions: '644' },
        { path: '/partner', exists: true, readable: true, writable: false, permissions: '644' }
      ],
      routes: [
        { path: '/admin', status: 'ok', response_code: 200 },
        { path: '/athlete', status: 'ok', response_code: 200 },
        { path: '/collaborator', status: 'ok', response_code: 200 },
        { path: '/partner', status: 'ok', response_code: 200 },
        { path: '/login', status: 'ok', response_code: 200 }
      ],
      logs: []
    };
  };

  const generateMockLogs = (): LogEntry[] => {
    return [
      {
        timestamp: new Date().toISOString(),
        level: 'info',
        message: 'Sistema iniciado com sucesso',
        context: { module: 'system' }
      },
      {
        timestamp: new Date(Date.now() - 3600000).toISOString(),
        level: 'warning',
        message: 'Upload de arquivo grande detectado',
        context: { file_size: '15MB', user_id: '123' }
      },
      {
        timestamp: new Date(Date.now() - 7200000).toISOString(),
        level: 'error',
        message: 'Falha na conexão com banco de dados',
        context: { attempts: 3, duration: '30s' }
      }
    ];
  };

  useEffect(() => {
    loadSystemStatus();
    loadLogs();
  }, []);

  const loadSystemStatus = async () => {
    setLoading(true);
    try {
      // Em produção, esta seria uma chamada real para a API
      await new Promise(resolve => setTimeout(resolve, 1000));
      setSystemStatus(generateMockSystemStatus());
    } catch (error) {
      console.error('Erro ao carregar status do sistema:', error);
    } finally {
      setLoading(false);
    }
  };

  const loadLogs = async () => {
    try {
      setLogs(generateMockLogs());
    } catch (error) {
      console.error('Erro ao carregar logs:', error);
    }
  };

  const refreshStatus = async () => {
    setRefreshing(true);
    await loadSystemStatus();
    await loadLogs();
    setRefreshing(false);
  };

  const getStatusIcon = (status: 'online' | 'offline' | 'error' | 'ok' | 'redirect') => {
    switch (status) {
      case 'online':
      case 'ok':
        return <CheckCircle className="w-5 h-5 text-success" />;
      case 'offline':
      case 'error':
        return <XCircle className="w-5 h-5 text-destructive" />;
      case 'redirect':
        return <AlertTriangle className="w-5 h-5 text-warning" />;
      default:
        return <XCircle className="w-5 h-5 text-muted-foreground" />;
    }
  };

  const getStatusBadge = (status: string, hasIssues = false) => {
    if (hasIssues) {
      return <Badge variant="destructive">Com Problemas</Badge>;
    }
    
    switch (status) {
      case 'online':
      case 'ok':
        return <Badge variant="success">Online</Badge>;
      case 'offline':
      case 'error':
        return <Badge variant="destructive">Offline</Badge>;
      default:
        return <Badge variant="secondary">Desconhecido</Badge>;
    }
  };

  const filteredLogs = logs.filter(log => 
    logFilter === 'all' || log.level === logFilter
  );

  const criticalIssuesCount = systemStatus ? [
    ...(systemStatus.database.missing_tables.length > 0 ? ['Tabelas em falta'] : []),
    ...(systemStatus.php.missing_extensions.length > 0 ? ['Extensões PHP em falta'] : []),
    ...systemStatus.directories.filter(d => !d.exists || !d.readable || !d.writable).map(d => `Diretório ${d.path}`),
    ...systemStatus.routes.filter(r => r.status === 'error').map(r => `Rota ${r.path}`)
  ] : [];

  if (loading) {
    return (
      <div className="space-y-6">
        <div className="flex items-center justify-between">
          <h1 className="text-3xl font-bold">Diagnóstico do Sistema</h1>
          <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
        </div>
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          {[...Array(4)].map((_, i) => (
            <Card key={i} className="animate-pulse">
              <CardContent className="p-6">
                <div className="h-4 bg-muted rounded w-3/4 mb-2"></div>
                <div className="h-8 bg-muted rounded w-1/2"></div>
              </CardContent>
            </Card>
          ))}
        </div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-bold">Diagnóstico do Sistema</h1>
          <p className="text-muted-foreground">Monitoramento completo da plataforma Plash</p>
        </div>
        <Button onClick={refreshStatus} disabled={refreshing} variant="outline">
          <RefreshCw className={`w-4 h-4 mr-2 ${refreshing ? 'animate-spin' : ''}`} />
          Atualizar
        </Button>
      </div>

      {/* Critical Issues Alert */}
      {criticalIssuesCount.length > 0 && (
        <Alert variant="destructive">
          <AlertTriangle className="h-4 w-4" />
          <AlertDescription>
            <strong>{criticalIssuesCount.length} problema(s) crítico(s) detectado(s):</strong>
            <ul className="mt-2 list-disc list-inside">
              {criticalIssuesCount.map((issue, index) => (
                <li key={index}>{issue}</li>
              ))}
            </ul>
          </AlertDescription>
        </Alert>
      )}

      {/* Status Overview */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <Card>
          <CardContent className="p-6">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-muted-foreground">Banco de Dados</p>
                <div className="flex items-center mt-2">
                  {getStatusIcon(systemStatus?.database.status || 'error')}
                  <span className="ml-2 text-2xl font-bold">
                    {systemStatus?.database.tables_count || 0}
                  </span>
                </div>
                <p className="text-xs text-muted-foreground">tabelas ativas</p>
              </div>
              <Database className="w-8 h-8 text-muted-foreground" />
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardContent className="p-6">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-muted-foreground">Servidor</p>
                <div className="flex items-center mt-2">
                  <CheckCircle className="w-5 h-5 text-success" />
                  <span className="ml-2 text-2xl font-bold">OK</span>
                </div>
                <p className="text-xs text-muted-foreground">{systemStatus?.server.software}</p>
              </div>
              <Server className="w-8 h-8 text-muted-foreground" />
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardContent className="p-6">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-muted-foreground">Diretórios</p>
                <div className="flex items-center mt-2">
                  {systemStatus?.directories.every(d => d.exists && d.readable) 
                    ? <CheckCircle className="w-5 h-5 text-success" />
                    : <XCircle className="w-5 h-5 text-destructive" />
                  }
                  <span className="ml-2 text-2xl font-bold">
                    {systemStatus?.directories.filter(d => d.exists).length || 0}
                  </span>
                </div>
                <p className="text-xs text-muted-foreground">de {systemStatus?.directories.length || 0} encontrados</p>
              </div>
              <FolderOpen className="w-8 h-8 text-muted-foreground" />
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardContent className="p-6">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-muted-foreground">Rotas</p>
                <div className="flex items-center mt-2">
                  {systemStatus?.routes.every(r => r.status === 'ok')
                    ? <CheckCircle className="w-5 h-5 text-success" />
                    : <XCircle className="w-5 h-5 text-destructive" />
                  }
                  <span className="ml-2 text-2xl font-bold">
                    {systemStatus?.routes.filter(r => r.status === 'ok').length || 0}
                  </span>
                </div>
                <p className="text-xs text-muted-foreground">de {systemStatus?.routes.length || 0} funcionando</p>
              </div>
              <Route className="w-8 h-8 text-muted-foreground" />
            </div>
          </CardContent>
        </Card>
      </div>

      {/* Detailed Information */}
      <Tabs defaultValue="database" className="space-y-4">
        <TabsList className="grid w-full grid-cols-6">
          <TabsTrigger value="database">Banco</TabsTrigger>
          <TabsTrigger value="php">PHP</TabsTrigger>
          <TabsTrigger value="server">Servidor</TabsTrigger>
          <TabsTrigger value="directories">Diretórios</TabsTrigger>
          <TabsTrigger value="routes">Rotas</TabsTrigger>
          <TabsTrigger value="logs">Logs</TabsTrigger>
        </TabsList>

        <TabsContent value="database">
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center">
                <Database className="w-5 h-5 mr-2" />
                Status do Banco de Dados
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="flex items-center justify-between">
                <span>Status da Conexão</span>
                {getStatusBadge(systemStatus?.database.status || 'error')}
              </div>
              <div className="flex items-center justify-between">
                <span>Tabelas Encontradas</span>
                <Badge variant="info">{systemStatus?.database.tables_count || 0}</Badge>
              </div>
              {systemStatus?.database.missing_tables && systemStatus.database.missing_tables.length > 0 && (
                <div>
                  <p className="font-medium text-destructive mb-2">Tabelas Ausentes:</p>
                  <ul className="space-y-1">
                    {systemStatus.database.missing_tables.map((table, index) => (
                      <li key={index} className="flex items-center">
                        <XCircle className="w-4 h-4 text-destructive mr-2" />
                        {table}
                      </li>
                    ))}
                  </ul>
                  <Button variant="warning" className="mt-4">
                    <Settings className="w-4 h-4 mr-2" />
                    Executar Migração
                  </Button>
                </div>
              )}
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="php">
          <Card>
            <CardHeader>
              <CardTitle>Ambiente PHP</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <p className="font-medium">Versão</p>
                  <p className="text-2xl font-bold text-success">{systemStatus?.php.version}</p>
                </div>
                <div>
                  <p className="font-medium">Limite de Memória</p>
                  <p className="text-2xl font-bold">{systemStatus?.php.memory_limit}</p>
                </div>
                <div>
                  <p className="font-medium">Tempo Máximo</p>
                  <p className="text-2xl font-bold">{systemStatus?.php.max_execution_time}s</p>
                </div>
              </div>
              
              <div>
                <p className="font-medium mb-2">Extensões Instaladas</p>
                <div className="flex flex-wrap gap-2">
                  {systemStatus?.php.extensions.map((ext, index) => (
                    <Badge key={index} variant="success">{ext}</Badge>
                  ))}
                </div>
              </div>

              {systemStatus?.php.missing_extensions && systemStatus.php.missing_extensions.length > 0 && (
                <div>
                  <p className="font-medium text-destructive mb-2">Extensões Ausentes</p>
                  <div className="flex flex-wrap gap-2">
                    {systemStatus.php.missing_extensions.map((ext, index) => (
                      <Badge key={index} variant="destructive">{ext}</Badge>
                    ))}
                  </div>
                </div>
              )}
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="server">
          <Card>
            <CardHeader>
              <CardTitle>Informações do Servidor</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <p className="font-medium">Software</p>
                  <p className="text-lg">{systemStatus?.server.software}</p>
                </div>
                <div>
                  <p className="font-medium">Documento Root</p>
                  <p className="text-lg font-mono text-sm">{systemStatus?.server.document_root}</p>
                </div>
              </div>
              
              <div>
                <p className="font-medium mb-2">Espaço em Disco</p>
                <div className="space-y-2">
                  <div className="flex justify-between text-sm">
                    <span>Usado: {systemStatus?.server.disk_space.used}</span>
                    <span>Livre: {systemStatus?.server.disk_space.free}</span>
                  </div>
                  <Progress value={30} className="h-2" />
                  <p className="text-xs text-muted-foreground">
                    Total: {systemStatus?.server.disk_space.total}
                  </p>
                </div>
              </div>

              <div className="flex gap-2">
                <Button variant="outline">
                  <Trash2 className="w-4 h-4 mr-2" />
                  Limpar Cache
                </Button>
                <Button variant="outline">
                  <RefreshCw className="w-4 h-4 mr-2" />
                  Limpar Sessões
                </Button>
              </div>
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="directories">
          <Card>
            <CardHeader>
              <CardTitle>Status dos Diretórios</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="space-y-3">
                {systemStatus?.directories.map((dir, index) => (
                  <div key={index} className="flex items-center justify-between p-3 border rounded-lg">
                    <div className="flex items-center">
                      <FolderOpen className="w-5 h-5 mr-3 text-muted-foreground" />
                      <div>
                        <p className="font-mono text-sm">{dir.path}</p>
                        <p className="text-xs text-muted-foreground">Permissões: {dir.permissions}</p>
                      </div>
                    </div>
                    <div className="flex items-center space-x-2">
                      {dir.exists ? (
                        <Badge variant="success">Existe</Badge>
                      ) : (
                        <Badge variant="destructive">Ausente</Badge>
                      )}
                      {dir.readable ? (
                        <Badge variant="info">Leitura</Badge>
                      ) : (
                        <Badge variant="warning">Sem Leitura</Badge>
                      )}
                      {dir.writable ? (
                        <Badge variant="success">Escrita</Badge>
                      ) : (
                        <Badge variant="warning">Sem Escrita</Badge>
                      )}
                    </div>
                  </div>
                ))}
              </div>
              
              <div className="mt-6">
                <Button variant="warning" className="mr-2">
                  <Shield className="w-4 h-4 mr-2" />
                  Corrigir Permissões
                </Button>
                <Button variant="outline">
                  <FolderOpen className="w-4 h-4 mr-2" />
                  Criar Diretórios
                </Button>
              </div>
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="routes">
          <Card>
            <CardHeader>
              <CardTitle>Status das Rotas</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="space-y-3">
                {systemStatus?.routes.map((route, index) => (
                  <div key={index} className="flex items-center justify-between p-3 border rounded-lg">
                    <div className="flex items-center">
                      <Route className="w-5 h-5 mr-3 text-muted-foreground" />
                      <div>
                        <p className="font-mono text-sm">{route.path}</p>
                        {route.error_message && (
                          <p className="text-xs text-destructive">{route.error_message}</p>
                        )}
                      </div>
                    </div>
                    <div className="flex items-center space-x-2">
                      {getStatusIcon(route.status)}
                      <Badge variant={route.status === 'ok' ? 'success' : 'destructive'}>
                        {route.response_code}
                      </Badge>
                    </div>
                  </div>
                ))}
              </div>
              
              <div className="mt-6">
                <Button variant="outline">
                  <RefreshCw className="w-4 h-4 mr-2" />
                  Testar Todas as Rotas
                </Button>
              </div>
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="logs">
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center justify-between">
                <span>Logs do Sistema</span>
                <div className="flex items-center space-x-2">
                  <select
                    value={logFilter}
                    onChange={(e) => setLogFilter(e.target.value as any)}
                    className="px-3 py-1 border rounded-md text-sm"
                  >
                    <option value="all">Todos</option>
                    <option value="error">Erros</option>
                    <option value="warning">Avisos</option>
                    <option value="info">Info</option>
                  </select>
                  <Button variant="outline" size="sm">
                    <Download className="w-4 h-4 mr-2" />
                    Exportar
                  </Button>
                  <Button variant="destructive" size="sm">
                    <Trash2 className="w-4 h-4 mr-2" />
                    Limpar
                  </Button>
                </div>
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div className="space-y-3 max-h-96 overflow-y-auto">
                {filteredLogs.map((log, index) => (
                  <div key={index} className="p-3 border rounded-lg">
                    <div className="flex items-center justify-between mb-2">
                      <div className="flex items-center">
                        {log.level === 'error' && <XCircle className="w-4 h-4 text-destructive mr-2" />}
                        {log.level === 'warning' && <AlertTriangle className="w-4 h-4 text-warning mr-2" />}
                        {log.level === 'info' && <CheckCircle className="w-4 h-4 text-info mr-2" />}
                        <Badge variant={log.level === 'error' ? 'destructive' : log.level === 'warning' ? 'warning' : 'info'}>
                          {log.level.toUpperCase()}
                        </Badge>
                      </div>
                      <span className="text-xs text-muted-foreground">
                        {new Date(log.timestamp).toLocaleString()}
                      </span>
                    </div>
                    <p className="text-sm">{log.message}</p>
                    {log.context && (
                      <pre className="text-xs text-muted-foreground mt-2 bg-muted p-2 rounded overflow-x-auto">
                        {JSON.stringify(log.context, null, 2)}
                      </pre>
                    )}
                  </div>
                ))}
                {filteredLogs.length === 0 && (
                  <div className="text-center py-8 text-muted-foreground">
                    <FileText className="w-8 h-8 mx-auto mb-2" />
                    <p>Nenhum log encontrado para o filtro selecionado</p>
                  </div>
                )}
              </div>
            </CardContent>
          </Card>
        </TabsContent>
      </Tabs>
    </div>
  );
};